<?php

namespace App\Http\Controllers;

use App\Models\LibHelp;
use App\Models\QuickResponse;
use App\Models\User;
use App\Mail\QuickResponseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class QuickResponseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                $user = auth()->user();
                
                $method = $request->route()->getActionMethod();
                $action = 'view'; // default
                
                if ($method === 'store') $action = 'add';
                
                abort_if(!$user->hasPermission('alalayang_agila', $action), 403, 'Unauthorized action.');
                
                return $next($request);
            }
        ];
    }
    public function index()
    {
        $help_list = LibHelp::all();
        return view('quick_response', compact('help_list'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lib_help_id' => 'required|exists:lib_help,id',
            'details' => 'required|string',
            'location' => 'nullable|string',
        ]);

        $quickResponse = QuickResponse::create([
            'user_id' => auth()->id(),
            'lib_help_id' => $validated['lib_help_id'],
            'details' => $validated['details'],
            'location' => $validated['location'],
        ]);

        // Get all user emails
        $emails = User::pluck('email')->toArray();

        // Send email to all members
        if (!empty($emails)) {
            Mail::to($emails)->send(new QuickResponseNotification($quickResponse));
        }

        // Send Telegram Notification
        $this->sendTelegramNotification($quickResponse);

        return redirect()->route('dashboard')->with('status', 'Your Alalayang Agila help request has been submitted successfully!');
    }

    private function sendTelegramNotification($quickResponse)
    {
        $telegramToken = '8555688646:AAFRitSezZXmTSeXtSxpLOK1BLHQ1qyE-KE';
        $chatId = '-1003711130933';

        $user = auth()->user();
        $helpType = $quickResponse->libHelp->name;
        $details = $quickResponse->details;
        $location = $quickResponse->location;
        $region = $user->region->name ?? 'N/A';
        $club = $user->club->name ?? 'N/A';

        // Escape Markdown special characters for robustness
        $helpType = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $helpType);
        $fullName = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $user->fullname);
        $region = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $region);
        $club = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $club);
        $details = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $details);

        $message = "🚨 *EMERGENCY ALERT* 🚨\n\n";
        $message .= "*Type:* " . $helpType . "\n";
        $message .= "*From:* Kuya " . $fullName . "\n";
        $message .= "*Region:* " . $region . "\n";
        $message .= "*Club:* " . $club . "\n";
        $message .= "*Details:* " . $details . "\n\n";

        if ($location) {
            $message .= "*Location:* [View on Google Maps](https://www.google.com/maps?q=" . $location . ")";
        }

        try {
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                // Send with photo as a file upload (more reliable than URL)
                Http::attach('photo', file_get_contents(storage_path('app/public/' . $user->profile_photo)), 'photo.jpg')
                    ->post("https://api.telegram.org/bot{$telegramToken}/sendPhoto", [
                        'chat_id' => $chatId,
                        'caption' => $message,
                        'parse_mode' => 'MarkdownV2',
                    ]);
            } else {
                // Send text only
                Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'MarkdownV2',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Telegram notification failed: " . $e->getMessage());
        }
    }
}
