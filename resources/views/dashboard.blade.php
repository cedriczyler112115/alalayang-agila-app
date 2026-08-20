@extends('layouts.app')

@section('title', 'Dashboard - Caragados EC')

@section('content')
    @php
        $premiumFeatureLockEnabled = \App\Models\AppSetting::isPremiumFeatureLockEnabled();
        $canViewAlalayangAgila = auth()->user()->hasPermission('alalayang_agila', 'view');
        $canViewSearchKuya = auth()->user()->hasPermission('search_kuya', 'view');
        $canViewMemberMapping = auth()->user()->hasPermission('member_mapping', 'view');
        $canViewAnnouncements = auth()->user()->hasPermission('announcements', 'view');
        $canViewLibraries = auth()->user()->hasPermission('libraries', 'view');
        $canUseChat = auth()->user()->canUseChatFeature();
        $canAccessAnnouncementsModule = !$premiumFeatureLockEnabled || (auth()->user()->canUseSubscriptionFeature('announcements') && $canViewAnnouncements);
    @endphp
    <style>
        .quick-link-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--accent) !important;
        }

        /* Announcement Carousel Styles */
        .announcement-carousel {
            position: relative;
            overflow: hidden;
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2.5rem;
        }

        .announcement-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .announcement-slide {
            min-width: 100%;
            padding: 2.5rem;
            box-sizing: border-box;
        }

        .carousel-nav {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding-bottom: 1.5rem;
        }

        .nav-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--border-color);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .nav-dot.active {
            background-color: var(--accent);
        }

        /* CKEditor Content Rendering Styles */
        .ck-content img {
            max-width: 100%;
            height: auto !important;
            display: block;
            margin: 1rem 0;
            border-radius: var(--radius-md);
        }

        .ck-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .ck-content table td,
        .ck-content table th {
            border: 1px solid var(--border-color);
            padding: 0.5rem;
        }

        .ck-content blockquote {
            border-left: 4px solid var(--accent);
            padding-left: 1rem;
            margin-left: 0;
            font-style: italic;
            color: var(--text-muted);
        }

        .announcement-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--accent);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        /* Tabs Styles */
        .announcement-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0px;
        }

        .announcement-tab-btn {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .announcement-tab-btn:hover {
            color: var(--accent);
        }

        .announcement-tab-btn.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        .announcement-tab-pane {
            display: none;
        }

        .announcement-tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .announcement-stack {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .announcement-card {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .announcement-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--accent);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes blinkWarning {

            0%,
            100% {
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
        <h1 style="font-size: 1.2rem; font-weight: 500; margin-bottom: 0.5rem; letter-spacing: -0.025em;">Welcome back,
            <span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem;">Kuya</span> <span
                style="color: var(--accent);">{{ auth()->user()->fullname }}</span>!
        </h1>
        <p style="color: var(--text-muted); margin-bottom: 2.5rem; font-size: 1.05rem;">Here is an overview of your
            CaragaDos Eagles Club dashboard.</p>

        @if(session('status'))
            <div
                style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <!-- Latest Announcements Section (Tabs) -->
        <div style="margin-bottom: 2.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2
                    style="font-size: 0.9rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0;">
                    Latest Announcements</h2>
                @if($canAccessAnnouncementsModule)
                    <a href="{{ route('announcements.index') }}"
                        style="font-size: 0.8rem; color: var(--accent); text-decoration: none; font-weight: 600;">View All
                        Announcements</a>
                @endif
            </div>

            <div
                style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
                <div class="announcement-tabs" id="announcementTabs" style="border-bottom: none; margin-bottom: 0;">
                    <button class="announcement-tab-btn active" data-target="tab-global">Global</button>
                    <button class="announcement-tab-btn" data-target="tab-regional">Regional</button>
                    <button class="announcement-tab-btn" data-target="tab-club">My Club</button>
                </div>
                <button type="button" onclick="document.getElementById('setupGuideModal').style.display='flex'"
                    class="btn btn-outline btn-sm"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; border-color: var(--accent); color: var(--accent); font-weight: 600; padding: 0.5rem 1rem; border-radius: var(--radius-md); margin-bottom: 0.25rem;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    How to Setup Alalayang Agila Community App
                </button>
            </div>

            <div class="tab-content">
                <!-- Global Tab -->
                <div id="tab-global" class="announcement-tab-pane active">
                    <div class="announcement-stack">
                        @forelse($global_announcements->take(3) as $announcement)
                            @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Global', 'canOpenAnnouncements' => $canAccessAnnouncementsModule])
                        @empty
                            <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;">
                                    <path
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10">
                                    </path>
                                </svg>
                                <p style="color: var(--text-muted); font-size: 1rem;">No global announcements published yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Regional Tab -->
                <div id="tab-regional" class="announcement-tab-pane">
                    <div class="announcement-stack">
                        @forelse($regional_announcements->take(3) as $announcement)
                            @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Regional', 'canOpenAnnouncements' => $canAccessAnnouncementsModule])
                        @empty
                            <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;">
                                    <path
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10">
                                    </path>
                                </svg>
                                <p style="color: var(--text-muted); font-size: 1rem;">No regional announcements published yet.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Club Tab -->
                <div id="tab-club" class="announcement-tab-pane">
                    <div class="announcement-stack">
                        @forelse($club_announcements->take(3) as $announcement)
                            @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Club', 'canOpenAnnouncements' => $canAccessAnnouncementsModule])
                        @empty
                            <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;">
                                    <path
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10">
                                    </path>
                                </svg>
                                <p style="color: var(--text-muted); font-size: 1rem;">No club announcements published yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Codes for Notification Section -->
        <div style="margin-bottom: 2.5rem;">
            <h2
                style="font-size: 0.9rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                Telegram QR Codes & App Setup for Notification
            </h2>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; justify-content: center; align-items: stretch;">

                <!-- Card 1: Step 1 Ribbon -->
                <div class="card"
                    style="position: relative; overflow: hidden; background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2rem 1.75rem 1.75rem 1.75rem; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">

                    <!-- Ribbon -->
                    <div
                        style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; padding: 0.4rem 1.1rem; border-bottom-right-radius: 12px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 2;">
                        STEP 1
                    </div>

                    <div
                        style="width: 100%; display: flex; justify-content: center; margin-bottom: 1rem; margin-top: 0.5rem;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode('https://t.me/+uegRcW7xnsNiYzI1') }}"
                            alt="Alalayang Agila Telegram QR Code"
                            style="max-width: 180px; width: 100%; height: auto; border: none; outline: none; display: block; border-radius: 8px;">
                    </div>
                    <h3
                        style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">
                        ALALAYANG AGILA
                    </h3>
                    <p
                        style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 1.25rem 0; flex-grow: 1;">
                        Scan this QR code to join the official <strong>Telegram Quick Response & Emergency
                            Notification</strong> group. Receive instant real-time alerts for urgent situations and
                        community assistance.
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
                    <div
                        style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; padding: 0.4rem 1.1rem; border-bottom-right-radius: 12px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 2;">
                        STEP 2
                    </div>

                    @if(!empty($userTelegramLink))
                        <div
                            style="width: 100%; display: flex; justify-content: center; margin-bottom: 1rem; margin-top: 0.5rem;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode($userTelegramLink) }}"
                                alt="Club Announcement Telegram QR Code"
                                style="max-width: 180px; width: 100%; height: auto; border: none; outline: none; display: block; border-radius: 8px;">
                        </div>
                        <h3
                            style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">
                            CLUB ANNOUNCEMENT
                        </h3>
                        @if(!empty($userClubName))
                            <span
                                style="font-size: 0.85rem; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">
                                ({{ $userClubName }})
                            </span>
                        @endif
                        <div class="warning-blinking"
                            style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: var(--radius-md); padding: 0.6rem 0.85rem; margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; width: 100%;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                style="flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <span>For Club Member Only!</span>
                        </div>
                        <p
                            style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 1.25rem 0; flex-grow: 1;">
                            Scan this QR code to join your dedicated <strong>Club Announcement Channel</strong> in Telegram to
                            receive official news, local updates, and event schedules for your club.
                        </p>
                        <a href="{{ $userTelegramLink }}" target="_blank" class="btn btn-outline btn-sm"
                            style="width: 100%; justify-content: center; border-color: var(--accent); color: var(--accent); font-weight: 600; text-decoration: none; padding: 0.5rem 1rem;">
                            Join Club Channel
                        </a>
                    @else
                        <div
                            style="width: 100%; flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 0 1rem 0;">
                            <div
                                style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #ef4444;">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3
                                style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">
                                CLUB ANNOUNCEMENT
                            </h3>
                            @if(!empty($userClubName))
                                <span
                                    style="font-size: 0.85rem; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 0.75rem; display: block;">
                                    ({{ $userClubName }})
                                </span>
                            @endif
                            <p style="font-size: 0.85rem; color: #ef4444; line-height: 1.5; margin: 0; max-width: 280px;">
                                No Telegram link configured for your club yet. We will notify you once the Telegram Group Bot
                                Chat is available.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Card 3: Step 3 Ribbon -->
                <div class="card"
                    style="position: relative; overflow: hidden; background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2rem 1.75rem 1.75rem 1.75rem; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">

                    <!-- Ribbon -->
                    <div
                        style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; padding: 0.4rem 1.1rem; border-bottom-right-radius: 12px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 2;">
                        STEP 3
                    </div>

                    <div
                        style="width: 100%; display: flex; justify-content: center; margin-bottom: 1rem; margin-top: 0.5rem;">
                        <img src="{{ asset('storage/ntfy-app-logo.jpg') }}" alt="ntfy Push Notification App Logo"
                            style="max-width: 180px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); display: block;">
                    </div>
                    <h3
                        style="font-size: 1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">
                        NTFY PUSH NOTIFICATIONS
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.4; margin: 0 0 1rem 0;">
                        Install <strong>ntfy</strong> on your phone, tap <strong>"+"</strong> to add subscriptions, and copy the
                        topic names below:
                    </p>

                    <div style="width: 100%; display: flex; flex-direction: column; gap: 0.75rem; margin-top: auto;">
                        <!-- Topic 1 -->
                        <div
                            style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; text-align: left;">
                            <div
                                style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                                1. Regional Announcements:
                            </div>
                            @if(!empty($userRegionKeyword))
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                    <code
                                        style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--accent); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $userRegionKeyword }}</code>
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
                        <div
                            style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; text-align: left;">
                            <div
                                style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                                2. Quick Response & Emergency Alerts:
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                <code
                                    style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--accent); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ALALAYANG-AGILA-TFOE-PE-2026</code>
                                <button type="button" onclick="copyTopicToClipboard('ALALAYANG-AGILA-TFOE-PE-2026', this)"
                                    class="btn btn-sm btn-outline"
                                    style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--accent); color: var(--accent); font-weight: 600; flex-shrink: 0;">
                                    Copy Topic
                                </button>
                            </div>
                        </div>

                        <!-- Topic 3 (Club Topic) -->
                        @if(!empty($userClubKeyword))
                            <div
                                style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; text-align: left;">
                                <div
                                    style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                                    3. Club Announcements Topic @if(!empty($userClubName))({{ $userClubName }})@endif:
                                </div>
                                <div class="warning-blinking"
                                    style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: var(--radius-md); padding: 0.4rem 0.6rem; margin-bottom: 0.35rem; display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; width: 100%;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24" style="flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <span>For Club Member Only!</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                    <code
                                        style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--accent); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $userClubKeyword }}</code>
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

        <!-- Quick Links Section -->
        <div style="margin-bottom: 2.5rem;">
            <h2
                style="font-size: 0.9rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                Quick Links</h2>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">

                <!-- Alalayang Agila Help Card -->
                @if(!$premiumFeatureLockEnabled || $canViewAlalayangAgila)
                    <a href="{{ route('quick.response') }}" class="card quick-link-card"
                        style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <div
                            style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #f59e0b;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Alalayang Agila Help</h3>
                    </a>
                @endif

                <!-- Group Chat Card -->
                @if(\App\Models\AppSetting::isChatWithKuyaEnabled() && (!$premiumFeatureLockEnabled || $canUseChat))
                    <a href="{{ route('chat.index') }}" class="card quick-link-card"
                        style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <div
                            style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #8b5cf6;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Group Chat</h3>
                    </a>
                @endif

                <!-- Search A Kuya Card -->
                @if(!$premiumFeatureLockEnabled || $canViewSearchKuya)
                    <a href="{{ route('search.kuya') }}" class="card quick-link-card"
                        style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <div
                            style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--accent);">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Search A Kuya</h3>
                    </a>
                @endif

                <!-- My Profile Card -->
                <a href="{{ route('profile.complete') }}" class="card quick-link-card"
                    style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                    <div
                        style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--success);">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">My Profile</h3>
                </a>

                <!-- Member Mapping Card -->
                @if(!$premiumFeatureLockEnabled || $canViewMemberMapping)
                    <a href="{{ route('profile.location') }}" class="card quick-link-card"
                        style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <div
                            style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(236, 72, 153, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #ec4899;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Member Mapping</h3>
                    </a>
                @endif

                <!-- Organizational Structure Card -->
                <a href="{{ route('org.structure') }}" class="card quick-link-card"
                    style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                    <div
                        style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(20, 184, 166, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #14b8a6;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Organizational Structure</h3>
                </a>

                <!-- Announcements Card -->
                @if($canAccessAnnouncementsModule)
                    <a href="{{ route('announcements.index') }}" class="card quick-link-card"
                        style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <div
                            style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--accent);">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                </path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Publish Announcement</h3>
                    </a>
                @endif

                <!-- Libraries Card -->
                @if($canViewLibraries)
                    <a href="{{ route('libraries.index') }}" class="card quick-link-card"
                        style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <div
                            style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #8b5cf6;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Libraries</h3>
                    </a>
                @endif
            </div>
        </div>

        <script>
            function copyTopicToClipboard(text, btnElement) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        showCopyFeedback(btnElement);
                    }).catch(() => {
                        fallbackCopy(text, btnElement);
                    });
                } else {
                    fallbackCopy(text, btnElement);
                }
            }

            function fallbackCopy(text, btnElement) {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showCopyFeedback(btnElement);
                } catch (err) {
                    console.error('Copy failed', err);
                }
                document.body.removeChild(textArea);
            }

            function showCopyFeedback(btnElement) {
                const originalText = btnElement.innerText;
                btnElement.innerText = 'Copied!';
                btnElement.style.background = 'var(--accent)';
                btnElement.style.color = '#fff';
                setTimeout(() => {
                    btnElement.innerText = originalText;
                    btnElement.style.background = 'transparent';
                    btnElement.style.color = 'var(--accent)';
                }, 2000);
            }

            document.addEventListener('DOMContentLoaded', function () {
                const tabBtns = document.querySelectorAll('.announcement-tab-btn');
                const tabPanes = document.querySelectorAll('.announcement-tab-pane');

                tabBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Remove active class from all
                        tabBtns.forEach(b => b.classList.remove('active'));
                        tabPanes.forEach(p => p.classList.remove('active'));

                        // Add active to clicked
                        btn.classList.add('active');
                        document.getElementById(btn.dataset.target).classList.add('active');
                    });
                });
            });
        </script>

        @if(session('from_login') || session('just_logged_in') || session('show_guide_modal'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = document.getElementById('setupGuideModal');
                    if (modal) {
                        modal.style.display = 'flex';
                    }
                });
            </script>
        @endif

        <!-- How to Setup Alalayang Agila Community App Modal -->
        <div id="setupGuideModal"
            style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); align-items: center; justify-content: center; z-index: 1000; padding: 1.25rem; backdrop-filter: blur(6px);">
            <div class="card"
                style="width: 100%; max-width: 760px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg); background: var(--card-bg);">

                <!-- Modal Header -->
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: flex-start; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: var(--card-bg);">
                    <div>
                        <h3 class="card-title"
                            style="font-size: 1.15rem; font-weight: 700; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 10px; color: var(--text-main);">
                            <div
                                style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            How to Setup Alalayang Agila Community App
                        </h3>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">
                            Follow these simple step-by-step instructions to get started and enable push notifications.
                        </p>
                    </div>
                    <button type="button" onclick="document.getElementById('setupGuideModal').style.display='none'"
                        style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--text-muted); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                        onmouseout="this.style.background='none'">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="card-body"
                    style="overflow-y: auto; padding: 1.5rem; line-height: 1.65; font-size: 0.95rem; color: var(--text-main); display: flex; flex-direction: column; gap: 1.5rem;">

                    <!-- Step 1 Card -->
                    <div
                        style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                        <h4
                            style="font-size: 1rem; font-weight: 700; color: var(--accent); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: var(--accent); color: #fff; font-size: 0.85rem; font-weight: 700; flex-shrink: 0;">1</span>
                            Accessing the Alalayang Agila Community App
                        </h4>
                        <ol style="margin: 0; padding-left: 1.4rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <li>Open your web browser (e.g. Google Chrome or Safari) and visit:
                                <a href="https://app.tfoe-alalayangagila.org/" target="_blank"
                                    style="color: var(--accent); font-weight: 600; text-decoration: underline;">https://app.tfoe-alalayangagila.org/</a>
                            </li>
                            <li>Click <strong>"Continue with Google"</strong> to register or sign in with your Google
                                account.</li>
                            <li>Your account registration will be reviewed and approved by the administrator
                                (<strong>Malihao Hills EC Technical</strong>).</li>
                            <li>While waiting for approval, you can update your personal information in your profile.</li>
                            <li><strong>Important Requirement:</strong> Please ensure all profile details are filled out,
                                especially your <strong>Exact Address Location on the Map</strong>.</li>
                            <li>Once your account is approved, you will have full access to all features in the Alalayang
                                Agila Community App.</li>
                        </ol>
                    </div>

                    <!-- Step 2 Card -->
                    <div
                        style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                        <h4
                            style="font-size: 1rem; font-weight: 700; color: var(--accent); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: var(--accent); color: #fff; font-size: 0.85rem; font-weight: 700; flex-shrink: 0;">2</span>
                            Installing the Push Notification App (ntfy)
                        </h4>
                        <ol style="margin: 0; padding-left: 1.4rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <li>On your mobile phone (Android Google Play Store or iPhone App Store), search for and install
                                <strong>"ntfy"</strong>.
                                <div
                                    style="margin-top: 0.75rem; margin-bottom: 0.75rem; display: flex; justify-content: center; width: 100%;">
                                    <img src="{{ asset('storage/ntfy-app-logo.jpg') }}" alt="ntfy App Logo"
                                        style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); display: block;">
                                </div>
                            </li>
                            <li>Open the <strong>ntfy</strong> application on your phone.</li>
                            <li>Tap the <strong>"+" (Add Subscription)</strong> button located in the upper-right corner.
                            </li>
                            <li>
                                @if(!empty($userRegionKeyword))
                                    Type <code
                                        style="background: rgba(59, 130, 246, 0.1); color: var(--accent); padding: 3px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;">{{ $userRegionKeyword }}</code>
                                    as the topic name and tap <strong>Subscribe</strong> <em>(for Important Regional
                                        Announcements)</em>.
                                @else
                                    <span style="color: #ef4444; font-style: italic;">No notification topic configured for your
                                        region.</span>
                                @endif
                            </li>
                            <li>
                                Tap the <strong>"+"</strong> button again, type <code
                                    style="background: rgba(59, 130, 246, 0.1); color: var(--accent); padding: 3px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;">ALALAYANG-AGILA-TFOE-PE-2026</code>
                                as the topic name, and tap <strong>Subscribe</strong> <em>(for Quick Response & Emergency
                                    Alerts)</em>.
                            </li>
                            @if(!empty($userClubKeyword))
                                <li>
                                    Tap the <strong>"+"</strong> button again, type <code
                                        style="background: rgba(59, 130, 246, 0.1); color: var(--accent); padding: 3px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;">{{ $userClubKeyword }}</code>
                                    as the topic name, and tap <strong>Subscribe</strong> <em>(for {{ $userClubName ?? 'Club' }}
                                        Announcements)</em>.
                                </li>
                            @endif
                        </ol>
                    </div>

                    <!-- Step 3 Card -->
                    <div
                        style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                        <h4
                            style="font-size: 1rem; font-weight: 700; color: var(--accent); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: var(--accent); color: #fff; font-size: 0.85rem; font-weight: 700; flex-shrink: 0;">3</span>
                            Joining the Emergency & Quick Response Telegram Group
                        </h4>
                        <ol style="margin: 0; padding-left: 1.4rem; display: flex; flex-direction: column; gap: 0.6rem;">
                            <li>Download and install <strong>Telegram</strong> on your smartphone and set it up using your
                                mobile number.</li>
                            <li>
                                Join the official Quick Response group by opening this link:
                                <a href="https://t.me/+uegRcW7xnsNiYzI1" target="_blank"
                                    style="color: var(--accent); font-weight: 600; text-decoration: underline;">https://t.me/+uegRcW7xnsNiYzI1</a>
                                or by scanning the QR code below:
                            </li>
                        </ol>
                        <div
                            style="margin: 1.25rem auto 0 auto; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; max-width: 350px;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode('https://t.me/+uegRcW7xnsNiYzI1') }}"
                                alt="Alalayang Agila Telegram QR Code"
                                style="max-width: 300px; width: 100%; height: auto; border: none; outline: none; display: block; border-radius: 8px;">
                            <span
                                style="margin-top: 0.6rem; font-weight: 700; font-size: 0.9rem; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">ALALAYANG
                                AGILA</span>
                        </div>
                    </div>

                    <!-- Step 4 Card -->
                    <div
                        style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                        <h4
                            style="font-size: 1rem; font-weight: 700; color: var(--accent); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 10px;">
                            <span
                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: var(--accent); color: #fff; font-size: 0.85rem; font-weight: 700; flex-shrink: 0;">4</span>
                            Joining Your Club Announcement Telegram Group
                        </h4>
                        <ol style="margin: 0; padding-left: 1.4rem; display: flex; flex-direction: column; gap: 0.6rem;">
                            <li>Open <strong>Telegram</strong> on your smartphone.</li>
                            <li>
                                Join your specific club's announcement channel by using the link or scanning the QR code
                                below:
                                @if(!empty($userTelegramLink))
                                    <div style="margin-top: 0.4rem;">
                                        <a href="{{ $userTelegramLink }}" target="_blank"
                                            style="color: var(--accent); font-weight: 600; text-decoration: underline;">{{ $userTelegramLink }}</a>
                                    </div>
                                @else
                                    <div
                                        style="background-color: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444; padding: 0.85rem 1.15rem; border-radius: var(--radius-md); margin-top: 0.6rem; display: flex; align-items: flex-start; gap: 10px;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        <div style="font-size: 0.88rem; line-height: 1.4;">
                                            <div style="font-weight: 700; margin-bottom: 2px;">No Telegram link configured
                                                (PENDING REGISTRATION IN TELEGRAM) for your club.</div>
                                            <div style="font-size: 0.82rem; opacity: 0.9;">We will notify you once the Telegram
                                                Group Bot Chat is available.</div>
                                        </div>
                                    </div>
                                @endif
                            </li>
                        </ol>

                        @if(!empty($userTelegramLink))
                            <div
                                style="margin: 1.25rem auto 0 auto; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; max-width: 350px;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode($userTelegramLink) }}"
                                    alt="Club Announcement Telegram QR Code"
                                    style="max-width: 300px; width: 100%; height: auto; border: none; outline: none; display: block; border-radius: 8px;">
                                <span
                                    style="margin-top: 0.6rem; font-weight: 700; font-size: 0.9rem; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">CLUB
                                    ANNOUNCEMENT</span>
                                @if(!empty($userClubName))
                                    <span
                                        style="margin-top: 0.25rem; font-weight: 700; font-size: 0.85rem; color: var(--accent); text-transform: uppercase; letter-spacing: 0.04em; text-align: center;">({{ $userClubName }})</span>
                                @endif
                                <div class="warning-blinking"
                                    style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: var(--radius-md); padding: 0.5rem 0.75rem; margin-top: 0.6rem; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; width: 100%;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24" style="flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <span>Do not scan this if you are not a member of this club</span>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="card-footer"
                    style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; background: var(--card-bg);">
                    <button type="button" onclick="document.getElementById('setupGuideModal').style.display='none'"
                        class="btn btn-primary" style="padding: 0.5rem 1.5rem; font-weight: 600;">Got It, Close
                        Guide</button>
                </div>
            </div>
        </div>
@endsection