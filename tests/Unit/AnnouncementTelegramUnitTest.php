<?php

namespace Tests\Unit;

use App\Http\Controllers\AnnouncementController;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AnnouncementTelegramUnitTest extends TestCase
{
    public function test_telegram_notification_sent_only_for_global_scope(): void
    {
        Http::fake();
        Mail::fake();

        $controller = new AnnouncementController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('sendTelegramNotification');
        $method->setAccessible(true);

        $globalAnnouncement = new Announcement([
            'title' => 'Global Announcement',
            'content' => '<p>Global body</p>',
            'scope' => 'global',
        ]);
        $globalAnnouncement->setRelation('user', new User(['fullname' => 'Admin']));

        $method->invoke($controller, $globalAnnouncement);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot') &&
                str_contains($request['text'], 'Global Announcement');
        });

        Http::fake();

        $clubAnnouncement = new Announcement([
            'title' => 'Club Announcement',
            'content' => '<p>Club body</p>',
            'scope' => 'club',
        ]);
        $clubAnnouncement->setRelation('user', new User(['fullname' => 'Admin']));

        $method->invoke($controller, $clubAnnouncement);

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot');
        });
    }
}
