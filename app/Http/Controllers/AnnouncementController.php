<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Mail\AnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query()->where('status', 'published')->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        // Only show published announcements to regular users, or allow authors to see their drafts
        if ($announcement->status !== 'published' && $announcement->user_id !== Auth::id()) {
            abort(404);
        }

        return view('announcements.show', compact('announcement'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'post_on_my_club' => 'nullable',
            'post_as_regional' => 'nullable',
            'post_as_global' => 'nullable',
        ]);

        $validated['user_id'] = Auth::id();
        
        $isRegionalOfficer = Auth::user()->lib_regional_position_id !== null;
        if ($isRegionalOfficer && $request->has('post_as_global')) {
            $validated['scope'] = 'global';
        } elseif ($isRegionalOfficer && $request->has('post_as_regional')) {
            $validated['scope'] = 'regional';
        } else {
            $validated['scope'] = 'club';
        }

        $validated['lib_region_id'] = Auth::user()->lib_region_id;
        $validated['lib_club_name_id'] = Auth::user()->lib_club_name_id;
        
        // If published, set published_at if not provided
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        if ($validated['status'] === 'published') {
            $this->sendNotifications($announcement);
        }

        return redirect()->route('announcements.index')->with('status', 'Announcement created successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'post_on_my_club' => 'nullable',
            'post_as_regional' => 'nullable',
            'post_as_global' => 'nullable',
        ]);

        $isRegionalOfficer = Auth::user()->lib_regional_position_id !== null;
        if ($isRegionalOfficer && $request->has('post_as_global')) {
            $validated['scope'] = 'global';
        } elseif ($isRegionalOfficer && $request->has('post_as_regional')) {
            $validated['scope'] = 'regional';
        } else {
            $validated['scope'] = 'club';
        }

        $validated['lib_region_id'] = Auth::user()->lib_region_id;
        $validated['lib_club_name_id'] = Auth::user()->lib_club_name_id;

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $was_draft = $announcement->status === 'draft';
        $announcement->update($validated);

        if ($was_draft && $validated['status'] === 'published') {
            $this->sendNotifications($announcement);
        }

        return redirect()->route('announcements.index')->with('status', 'Announcement updated successfully!');
    }

    private function sendNotifications($announcement)
    {
        // Send email to all members
        $emails = User::pluck('email')->toArray();
        if (!empty($emails)) {
            Mail::to($emails)->send(new AnnouncementNotification($announcement));
        }

        // Send Telegram Notification
        $this->sendTelegramNotification($announcement);
    }

    private function sendTelegramNotification($announcement)
    {
        $telegramToken = '8555688646:AAFRitSezZXmTSeXtSxpLOK1BLHQ1qyE-KE';
        $chatId = '-1003711130933';

        $user = Auth::user();
        $title = $announcement->title;
        // Keep basic formatting tags for Telegram HTML mode
        $content = strip_tags($announcement->content, '<b><strong><i><em><u><a><code><pre>');
        
        // Convert <strong> to <b> and <em> to <i> for Telegram compatibility
        $content = str_replace(['<strong>', '</strong>', '<em>', '</em>'], ['<b>', '</b>', '<i>', '</i>'], $content);

        $message = "📢 <b>NEW ANNOUNCEMENT</b> 📢\n\n";
        $message .= "<b>Title:</b> " . htmlspecialchars($title) . "\n";
        $message .= "<b>Author:</b> Kuya " . htmlspecialchars($user->fullname) . "\n\n";
        $message .= $content . "\n\n";
        
        $dashboardUrl = route('dashboard');
        $message .= '';

        try {
            Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Exception $e) {
            \Log::error("Telegram announcement notification failed: " . $e->getMessage());
        }
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('status', 'Announcement deleted successfully!');
    }
}
