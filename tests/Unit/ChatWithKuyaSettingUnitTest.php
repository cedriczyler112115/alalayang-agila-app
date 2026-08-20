<?php

namespace Tests\Unit;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatWithKuyaSettingUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_with_kuya_setting_toggle(): void
    {
        AppSetting::putMany(['chat_with_kuya_enabled' => false]);
        $this->assertFalse(AppSetting::isChatWithKuyaEnabled());

        $user = new User(['is_admin' => false]);
        $this->assertFalse($user->canUseChatFeature());

        AppSetting::putMany(['chat_with_kuya_enabled' => true]);
        $this->assertTrue(AppSetting::isChatWithKuyaEnabled());
    }
}
