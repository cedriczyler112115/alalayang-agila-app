<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Announcement;
use App\Models\GlobalKeyword;
use App\Models\LibClubName;
use App\Models\LibRegion;
use App\Models\User;
use App\Mail\AnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AnnouncementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (!AppSetting::isPremiumFeatureLockEnabled()) {
                    return $next($request);
                }

                if ($request->route()->getActionMethod() === 'show') {
                    return $next($request);
                }

                $user = auth()->user();
                
                $method = $request->route()->getActionMethod();
                $action = 'view'; // default
                
                if (in_array($method, ['create', 'store'])) $action = 'add';
                if (in_array($method, ['edit', 'update'])) $action = 'edit';
                if ($method === 'destroy') $action = 'delete';
                
                abort_if(!$user->canUseSubscriptionFeature('announcements'), 403, 'Publish Announcement requires an active subscription.');
                abort_if(!$user->hasPermission('announcements', $action), 403, 'Unauthorized action.');
                
                return $next($request);
            }
        ];
    }
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

        $announcement->load([
            'user',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with(['user', 'replies'])
                    ->latest();
            },
        ]);

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
        
        if ($request->has('post_as_global')) {
            $validated['scope'] = 'global';
        } elseif ($request->has('post_as_regional')) {
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
            $this->sendNtfyNotification($announcement);
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

        if ($request->has('post_as_global')) {
            $validated['scope'] = 'global';
        } elseif ($request->has('post_as_regional')) {
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
            $this->sendNtfyNotification($announcement);
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
        if ($announcement->scope === 'global') {
            $this->sendTelegramNotification($announcement);
        }
    }

    private function sendTelegramNotification($announcement)
    {
        if ($announcement->scope !== 'global') {
            return;
        }

        $telegramToken = '8555688646:AAFRitSezZXmTSeXtSxpLOK1BLHQ1qyE-KE';
        $chatId = '-1003711130933';

        $user = $announcement->user ?? Auth::user();
        $title = $announcement->title;
        // Keep basic formatting tags for Telegram HTML mode
        $content = strip_tags($announcement->content, '<b><strong><i><em><u><a><code><pre>');
        
        // Convert <strong> to <b> and <em> to <i> for Telegram compatibility
        $content = str_replace(['<strong>', '</strong>', '<em>', '</em>'], ['<b>', '</b>', '<i>', '</i>'], $content);

        $message = "📢 <b>NEW ANNOUNCEMENT</b> 📢\n\n";
        $message .= "<b>Title:</b> " . htmlspecialchars($title) . "\n";
        $message .= "<b>Author:</b> Kuya " . htmlspecialchars($user?->fullname ?? 'N/A') . "\n\n";
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

    private function sendNtfyNotification(Announcement $announcement): void
    {
        $topic = $this->resolveNtfyTopic($announcement);

        if (!$topic) {
            \Log::warning('ntfy announcement notification skipped because no topic was found.', [
                'announcement_id' => $announcement->id,
                'scope' => $announcement->scope,
                'user_id' => Auth::id(),
            ]);
            return;
        }

        $user = Auth::user();
        $scopeLabel = match ($announcement->scope) {
            'global' => 'Global Announcement',
            'regional' => 'Regional Announcement',
            default => 'Club Announcement',
        };

        $regionName = $user?->region?->name ?? 'N/A';
        $clubName = $user?->club?->name ?? 'N/A';
        $publishedAt = $announcement->published_at?->format('F j, Y g:i A') ?? 'N/A';
        $message = "Title: {$announcement->title}\n";
        $message .= "Author: Kuya, " . ($user?->fullname ?? 'N/A') . "\n";
        $message .= "Region: {$regionName}\n";
        $message .= "Club: {$clubName}\n";
        $message .= "Published: {$publishedAt}\n\n";
        $content = html_entity_decode($announcement->content ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/<\\s*\\/p\\s*>/i', "\n", $content);
        $content = preg_replace('/<\\s*br\\s*\\/?>/i', "\n", $content);
        $content = preg_replace('/<\\s*\\/div\\s*>/i', "\n", $content);
        $content = preg_replace('/<\\s*\\/li\\s*>/i', "\n", $content);
        $content = preg_replace('/<\\s*p[^>]*>/i', '', $content);
        $content = preg_replace('/<\\s*div[^>]*>/i', '', $content);
        $content = preg_replace('/<\\s*li[^>]*>/i', '- ', $content);
        $content = strip_tags($content);
        $content = preg_replace("/\\r\\n|\\r/", "\n", $content);
        $content = preg_replace("/\n{3,}/", "\n", $content);
        $content = trim($content) ?: 'N/A';
        $content .= "\n\nRegards,\nKuya " . ($user?->fullname ?? 'N/A');
        $message .= $content;

        $title = match ($announcement->scope) {
            'global' => $announcement->title . ' [GLOBAL]',
            'regional' => $announcement->title . ' [REGION]',
            default => $announcement->title . ' [CLUB]',
        };

        try {
            Http::withHeaders([
                'Title' => $title,
                'Tags' => 'loudspeaker',
                'Priority' => '3',
                'Markdown' => 'yes',
            ])->withBody($message, 'text/markdown')->post("https://ntfy.sh/" . rawurlencode($topic));
        } catch (\Exception $e) {
            \Log::error('ntfy announcement notification failed: ' . $e->getMessage(), [
                'announcement_id' => $announcement->id,
                'topic' => $topic,
            ]);
        }
    }

    private function resolveNtfyTopic(Announcement $announcement): ?string
    {
        $user = Auth::user();

        if ($announcement->scope === 'global') {
            return GlobalKeyword::query()->value('keyword');
        }

        if ($announcement->scope === 'regional' && $user?->lib_region_id) {
            return LibRegion::query()
                ->whereKey($user->lib_region_id)
                ->value('notification_keyword');
        }

        if ($announcement->scope === 'club' && $user?->lib_club_name_id) {
            return LibClubName::query()
                ->whereKey($user->lib_club_name_id)
                ->value('notification_keyword');
        }

        return null;
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('status', 'Announcement deleted successfully!');
    }
}
