<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class SettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                abort_unless(auth()->user()?->is_admin, 403, 'Unauthorized action.');

                return $next($request);
            },
        ];
    }

    public function index()
    {
        return view('settings.index', [
            'settings' => AppSetting::allWithDefaults(),
            'premiumFeatures' => AppSetting::premiumFeatureLabels(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'premium_feature_lock_enabled' => 'nullable|boolean',
        ]);

        AppSetting::putMany([
            'premium_feature_lock_enabled' => (bool) ($validated['premium_feature_lock_enabled'] ?? false),
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
