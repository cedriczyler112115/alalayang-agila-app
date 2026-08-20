<?php

namespace Tests\Unit;

use App\Http\Controllers\AnnouncementController;
use App\Models\Announcement;
use App\Models\LibTelegram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnnouncementTelegramUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_telegram_notification(): void
    {
        Http::fake();

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
    }

    public function test_club_telegram_notification_using_lib_telegram_config(): void
    {
        Http::fake();

        LibTelegram::create([
            'club_id' => 5,
            'token' => 'test-token-123',
            'group_id' => -100123456789,
            't_group_name' => 'Test Club Group',
        ]);

        $controller = new AnnouncementController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('sendTelegramNotification');
        $method->setAccessible(true);

        $clubAnnouncement = new Announcement([
            'title' => 'Test Club Announcement',
            'content' => '<p>Club Announcement Body</p>',
            'scope' => 'club',
            'lib_club_name_id' => 5,
        ]);
        $user = new User(['fullname' => 'John Doe', 'lib_club_name_id' => 5]);
        $clubAnnouncement->setRelation('user', $user);

        $method->invoke($controller, $clubAnnouncement);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bottest-token-123/sendMessage') &&
                $request['chat_id'] === -100123456789 &&
                str_contains($request['text'], 'Test Club Announcement');
        });
    }
}
