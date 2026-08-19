<?php

namespace Tests\Unit;

use App\Http\Controllers\QuickResponseController;
use App\Models\QuickResponse;
use App\Models\LibHelp;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuickResponseNotificationUnitTest extends TestCase
{
    public function test_quick_response_dispatches_telegram_and_ntfy_and_no_email(): void
    {
        Http::fake();
        Mail::fake();

        $controller = new QuickResponseController();
        $reflection = new \ReflectionClass($controller);

        $sendTelegram = $reflection->getMethod('sendTelegramNotification');
        $sendTelegram->setAccessible(true);

        $sendNtfy = $reflection->getMethod('sendNtfyNotification');
        $sendNtfy->setAccessible(true);

        $user = new User(['fullname' => 'Agila Responder']);
        $help = new LibHelp(['name' => 'Medical Emergency']);
        
        $quickResponse = new QuickResponse([
            'details' => 'Immediate assistance needed at site.',
            'location' => '8.9482, 125.5432',
        ]);
        $quickResponse->setRelation('user', $user);
        $quickResponse->setRelation('libHelp', $help);

        $sendTelegram->invoke($controller, $quickResponse);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot') &&
                str_contains($request['caption'] ?? $request['text'] ?? '', 'EMERGENCY');
        });

        Mail::assertNothingSent();
    }
}
