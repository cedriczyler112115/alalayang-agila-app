<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\GlobalKeyword;
use App\Models\LibHelp;
use App\Models\QuickResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class QuickResponseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (!AppSetting::isPremiumFeatureLockEnabled()) {
                    return $next($request);
                }

                $user = auth()->user();

                $method = $request->route()->getActionMethod();
                $action = 'view';

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

        $quickResponse->load(['user.region', 'user.club', 'libHelp']);

        $this->sendTelegramNotification($quickResponse);
        $this->sendNtfyNotification($quickResponse);

        return redirect()->route('dashboard')->with('status', 'Your Alalayang Agila help request has been submitted successfully!');
    }

    private function sendNtfyNotification(QuickResponse $quickResponse): void
    {
        $topic = GlobalKeyword::where('desc', 'agila_help')->value('keyword');

        if (!$topic) {
            \Log::warning('ntfy quick response notification skipped because agila_help keyword was not found.', [
                'quick_response_id' => $quickResponse->id,
            ]);
            return;
        }

        $user = $quickResponse->user ?? auth()->user();
        $helpType = $quickResponse->libHelp->name ?? 'N/A';
        $details = $quickResponse->details;
        $location = $quickResponse->location;
        $region = $user?->region?->name ?? 'N/A';
        $club = $user?->club?->name ?? 'N/A';
        $iconUrl = $this->publicProfilePhotoUrl($user?->profile_photo);

        $message = "Alalayang Agila Help Request\n";
        $message .= "Type: {$helpType}\n";
        $message .= "From: Kuya " . ($user?->fullname ?? 'N/A') . "\n";
        $message .= "Region: {$region}\n";
        $message .= "Club: {$club}\n";
        $message .= "Details:\n{$details}\n";

        if ($location) {
            $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($location);
            $message .= "\nLocation: [View on Map]({$mapUrl})";
        }

        try {
            $headers = [
                'Title' => 'Alalayang Agila Help',
                'Tags' => 'rotating_light',
                'Priority' => '4',
                'Markdown' => 'yes',
            ];

            if ($iconUrl) {
                $headers['Icon'] = $iconUrl;
            }

            Http::withHeaders($headers)->withBody($message, 'text/markdown')->post("https://ntfy.sh/" . rawurlencode($topic));
        } catch (\Exception $e) {
            \Log::error("ntfy quick response notification failed: " . $e->getMessage(), [
                'quick_response_id' => $quickResponse->id,
                'topic' => $topic,
            ]);
        }
    }

    private function publicProfilePhotoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $relativePath = Storage::url($path);
        $baseUrl = rtrim(config('app.url') ?: '', '/');

        if ($baseUrl === '') {
            return asset(ltrim($relativePath, '/'));
        }

        return $baseUrl . '/' . ltrim($relativePath, '/');
    }

    private function sendTelegramNotification(QuickResponse $quickResponse): void
    {
        $telegramToken = '8555688646:AAFRitSezZXmTSeXtSxpLOK1BLHQ1qyE-KE';
        $chatId = '-1003711130933';

        $user = $quickResponse->user ?? auth()->user();
        $helpType = $quickResponse->libHelp->name ?? 'N/A';
        $details = $quickResponse->details ?? '';
        $location = $quickResponse->location;
        $region = $user?->region?->name ?? 'N/A';
        $club = $user?->club?->name ?? 'N/A';

        $helpType = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $helpType);
        $fullName = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'], $user?->fullname ?? 'N/A');
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
            $message .= "*Location:* [View on Google Maps](https://www.google.com/maps?q=" . rawurlencode($location) . ")";
        }

        try {
            if ($user?->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                Http::attach('photo', file_get_contents(storage_path('app/public/' . $user->profile_photo)), 'photo.jpg')
                    ->post("https://api.telegram.org/bot{$telegramToken}/sendPhoto", [
                        'chat_id' => $chatId,
                        'caption' => $message,
                        'parse_mode' => 'MarkdownV2',
                    ]);
            } else {
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
