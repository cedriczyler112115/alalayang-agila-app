@extends('layouts.app')

@section('title', 'Settings - Caragados EC')

@section('content')
<div style="padding: 2rem; width: 100%; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">System Settings</h1>
            <p style="color: var(--text-muted); margin: 0;">Manage the single premium feature lock for member services.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--success); padding: 1rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf

        <section style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.75rem;">
            <div style="margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Premium Feature Lock</h2>
                <p style="color: var(--text-muted); margin: 0;">
                    If this setting is <strong>ON</strong>, the system keeps the current premium restrictions.
                    If it is <strong>OFF</strong>, all users can access all premium features.
                </p>
            </div>

            <div class="settings-row">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">Enable Premium Feature Lock</h3>
                    <p style="color: var(--text-muted); margin: 0;">Turn this off to open all premium features to all users.</p>
                </div>
                <label class="settings-switch">
                    <input type="hidden" name="premium_feature_lock_enabled" value="0">
                    <input type="checkbox" name="premium_feature_lock_enabled" value="1" {{ !empty($settings['premium_feature_lock_enabled']) && in_array((string) $settings['premium_feature_lock_enabled'], ['1', 'true'], true) ? 'checked' : '' }}>
                    <span class="settings-slider"></span>
                </label>
            </div>

            <div class="settings-row">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">Enable "Chat with Kuya" Service</h3>
                    <p style="color: var(--text-muted); margin: 0;">Turn this off to hide and disable the "Chat with Kuya" service across the platform.</p>
                </div>
                <label class="settings-switch">
                    <input type="hidden" name="chat_with_kuya_enabled" value="0">
                    <input type="checkbox" name="chat_with_kuya_enabled" value="1" {{ !empty($settings['chat_with_kuya_enabled']) && in_array((string) $settings['chat_with_kuya_enabled'], ['1', 'true'], true) ? 'checked' : '' }}>
                    <span class="settings-slider"></span>
                </label>
            </div>

            <div style="margin-top: 1.5rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background-color: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.15);">
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">Affected Premium Features</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    @foreach($premiumFeatures as $feature)
                        <span style="display: inline-flex; align-items: center; padding: 0.45rem 0.8rem; border-radius: 999px; background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.92rem;">
                            {{ $feature }}
                        </span>
                    @endforeach
                </div>
            </div>
        </section>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="background-color: var(--accent); border-color: var(--accent); min-width: 200px;">
                Save Settings
            </button>
        </div>
    </form>
</div>

<style>
    .settings-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        padding: 1rem 0;
        border-top: 1px solid var(--border-color);
    }

    .settings-row:first-of-type {
        border-top: none;
        padding-top: 0;
    }

    .settings-switch {
        position: relative;
        display: inline-flex;
        width: 56px;
        height: 30px;
        flex: 0 0 auto;
    }

    .settings-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .settings-slider {
        position: absolute;
        inset: 0;
        cursor: pointer;
        background-color: rgba(148, 163, 184, 0.45);
        border-radius: 999px;
        transition: 0.2s ease;
    }

    .settings-slider::before {
        content: '';
        position: absolute;
        width: 24px;
        height: 24px;
        left: 3px;
        top: 3px;
        border-radius: 50%;
        background-color: #fff;
        box-shadow: var(--shadow-sm);
        transition: 0.2s ease;
    }

    .settings-switch input:checked + .settings-slider {
        background-color: var(--accent);
    }

    .settings-switch input:checked + .settings-slider::before {
        transform: translateX(26px);
    }

    @media (max-width: 768px) {
        .settings-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection
