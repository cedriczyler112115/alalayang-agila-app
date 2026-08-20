@extends('layouts.app')

@section('title', 'Telegram & Push Notification Setup - Caragados EC')

@section('content')
<style>
    @keyframes blinkWarning {
        0%, 100% {
            opacity: 1;
            background-color: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 0 12px rgba(239, 68, 68, 0.35);
        }
        50% {
            opacity: 0.5;
            background-color: rgba(239, 68, 68, 0.05);
            border-color: rgba(239, 68, 68, 0.2);
            box-shadow: none;
        }
    }

    .warning-blinking {
        animation: blinkWarning 1.2s infinite ease-in-out;
    }
</style>

<div style="margin-top: 2rem;">
    <!-- Page Header with Back Button -->
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding: 0.5rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;" title="Back to Dashboard">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; letter-spacing: -0.025em;">Telegram & <span style="color: var(--accent);">Notification Setup</span></h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Connect your Telegram account and configure mobile push notifications for instant alerts.</p>
        </div>
    </div>

    <!-- QR Codes & Notification Setup Section -->
    <div style="margin-bottom: 2.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; justify-content: center; align-items: stretch;">

            <!-- Card 1: Step 1 Ribbon -->
            <div class="card"
                style="position: relative; overflow: hidden; background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2rem 1.75rem 1.75rem 1.75rem; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">

                <!-- Ribbon -->
                <div style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; padding: 0.4rem 1.1rem; border-bottom-right-radius: 12px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 2;">
                    STEP 1
                </div>

                <div style="width: 100%; display: flex; justify-content: center; margin-bottom: 1rem; margin-top: 0.5rem;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode('https://t.me/+uegRcW7xnsNiYzI1') }}"
                        alt="Alalayang Agila Telegram QR Code"
                        style="max-width: 180px; width: 100%; height: auto; border: none; outline: none; display: block; border-radius: 8px;">
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">
                    ALALAYANG AGILA
                </h3>
                <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 1.25rem 0; flex-grow: 1;">
                    Scan this QR code to join the official <strong>Telegram Quick Response & Emergency Notification</strong> group. Receive instant real-time alerts for urgent situations and community assistance.
                </p>
                <a href="https://t.me/+uegRcW7xnsNiYzI1" target="_blank" class="btn btn-outline btn-sm"
                    style="width: 100%; justify-content: center; border-color: var(--accent); color: var(--accent); font-weight: 600; text-decoration: none; padding: 0.5rem 1rem;">
                    Join Quick Response Group
                </a>
            </div>

            <!-- Card 2: Step 2 Ribbon -->
            <div class="card"
                style="position: relative; overflow: hidden; background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2rem 1.75rem 1.75rem 1.75rem; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">

                <!-- Ribbon -->
                <div style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; padding: 0.4rem 1.1rem; border-bottom-right-radius: 12px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 2;">
                    STEP 2
                </div>

                @if(!empty($userTelegramLink))
                    <div style="width: 100%; display: flex; justify-content: center; margin-bottom: 1rem; margin-top: 0.5rem;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode($userTelegramLink) }}"
                            alt="Club Announcement Telegram QR Code"
                            style="max-width: 180px; width: 100%; height: auto; border: none; outline: none; display: block; border-radius: 8px;">
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">
                        CLUB ANNOUNCEMENT
                    </h3>
                    @if(!empty($userClubName))
                        <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                            ({{ $userClubName }})
                        </span>
                    @endif
                    <div class="warning-blinking"
                        style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: var(--radius-md); padding: 0.6rem 0.85rem; margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; width: 100%;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>For Club Member Only!</span>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 1.25rem 0; flex-grow: 1;">
                        Scan this QR code to join your dedicated <strong>Club Announcement Channel</strong> in Telegram to receive official news, local updates, and event schedules for your club.
                    </p>
                    <a href="{{ $userTelegramLink }}" target="_blank" class="btn btn-outline btn-sm"
                        style="width: 100%; justify-content: center; border-color: var(--accent); color: var(--accent); font-weight: 600; text-decoration: none; padding: 0.5rem 1rem;">
                        Join Club Channel
                    </a>
                @else
                    <div style="width: 100%; flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 0 1rem 0;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #ef4444;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">
                            CLUB ANNOUNCEMENT
                        </h3>
                        @if(!empty($userClubName))
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 0.75rem; display: block;">
                                ({{ $userClubName }})
                            </span>
                        @endif
                        <p style="font-size: 0.85rem; color: #ef4444; line-height: 1.5; margin: 0; max-width: 280px;">
                            No Telegram link configured for your club yet. We will notify you once the Telegram Group Bot Chat is available.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Card 3: Step 3 Ribbon -->
            <div class="card"
                style="position: relative; overflow: hidden; background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2rem 1.75rem 1.75rem 1.75rem; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">

                <!-- Ribbon -->
                <div style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; padding: 0.4rem 1.1rem; border-bottom-right-radius: 12px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 2;">
                    STEP 3
                </div>

                <div style="width: 100%; display: flex; justify-content: center; margin-bottom: 1rem; margin-top: 0.5rem;">
                    <img src="{{ asset('storage/ntfy-app-logo.jpg') }}" alt="ntfy Push Notification App Logo"
                        style="max-width: 180px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); display: block;">
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">
                    NTFY PUSH NOTIFICATIONS
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.4; margin: 0 0 1rem 0;">
                    Install <strong>ntfy</strong> on your phone, tap <strong>"+"</strong> to add subscriptions, and copy the topic names below:
                </p>

                <div style="width: 100%; display: flex; flex-direction: column; gap: 0.75rem; margin-top: auto;">
                    <!-- Topic 1 -->
                    <div style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; text-align: left;">
                        <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                            1. Regional Announcements:
                        </div>
                        @if(!empty($userRegionKeyword))
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                <code style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--accent); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $userRegionKeyword }}</code>
                                <button type="button" onclick="copyTopicToClipboard('{{ $userRegionKeyword }}', this)"
                                    class="btn btn-sm btn-outline"
                                    style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--accent); color: var(--accent); font-weight: 600; flex-shrink: 0;">
                                    Copy Topic
                                </button>
                            </div>
                        @else
                            <div style="font-size: 0.8rem; color: #ef4444; font-style: italic; margin-top: 0.2rem;">
                                No notification topic configured for your region.
                            </div>
                        @endif
                    </div>

                    <!-- Topic 2 -->
                    <div style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; text-align: left;">
                        <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                            2. Quick Response & Emergency Alerts:
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                            <code style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--accent); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ALALAYANG-AGILA-TFOE-PE-2026</code>
                            <button type="button" onclick="copyTopicToClipboard('ALALAYANG-AGILA-TFOE-PE-2026', this)"
                                class="btn btn-sm btn-outline"
                                style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--accent); color: var(--accent); font-weight: 600; flex-shrink: 0;">
                                Copy Topic
                            </button>
                        </div>
                    </div>

                    <!-- Topic 3 (Club Topic) -->
                    @if(!empty($userClubKeyword))
                        <div style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; text-align: left;">
                            <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                                3. Club Announcements Topic @if(!empty($userClubName))({{ $userClubName }})@endif:
                            </div>
                            <div class="warning-blinking"
                                style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: var(--radius-md); padding: 0.4rem 0.6rem; margin-bottom: 0.35rem; display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; width: 100%;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>For Club Member Only!</span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                <code style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--accent); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $userClubKeyword }}</code>
                                <button type="button" onclick="copyTopicToClipboard('{{ $userClubKeyword }}', this)"
                                    class="btn btn-sm btn-outline"
                                    style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--accent); color: var(--accent); font-weight: 600; flex-shrink: 0;">
                                    Copy Topic
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function copyTopicToClipboard(text, btnElement) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showCopyFeedback(btnElement);
            }).catch(() => {
                fallbackCopyText(text, btnElement);
            });
        } else {
            fallbackCopyText(text, btnElement);
        }
    }

    function fallbackCopyText(text, btnElement) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        try {
            document.execCommand('copy');
            showCopyFeedback(btnElement);
        } catch (err) {
            alert('Failed to copy topic: ' + text);
        }
        document.body.removeChild(tempInput);
    }

    function showCopyFeedback(btnElement) {
        const originalText = btnElement.innerText;
        btnElement.innerText = 'Copied!';
        btnElement.style.backgroundColor = 'var(--accent)';
        btnElement.style.color = '#ffffff';

        setTimeout(() => {
            btnElement.innerText = originalText;
            btnElement.style.backgroundColor = '';
            btnElement.style.color = 'var(--accent)';
        }, 2000);
    }
</script>
@endsection
