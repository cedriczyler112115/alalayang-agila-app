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

        /* Mobile App Segmented Control Tabs Styles */
        .announcement-tabs {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 9999px;
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 480px;
        }

        .announcement-tab-btn {
            flex: 1;
            padding: 0.6rem 1rem;
            font-weight: 700;
            font-size: 0.82rem;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            white-space: nowrap;
        }

        .announcement-tab-btn:hover {
            color: var(--text-main);
        }

        .announcement-tab-btn.active {
            color: #ffffff;
            background-color: var(--accent);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }

        @media (prefers-color-scheme: dark) {
            .announcement-tabs {
                background-color: rgba(255, 255, 255, 0.05);
            }
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

        /* Mobile App Square Icon Cards Grid */
        .app-services-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .app-service-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.25rem 0.5rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
            aspect-ratio: 1 / 1;
            text-align: center;
        }

        .app-service-card:hover,
        .app-service-card:active {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--accent);
        }

        .app-service-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.65rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .app-service-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.25;
            text-align: center;
        }

        @media (max-width: 992px) {
            .app-services-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 576px) {
            .app-services-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
            }

            .app-service-card {
                padding: 1rem 0.25rem;
            }

            .app-service-icon {
                width: 40px;
                height: 40px;
                margin-bottom: 0.4rem;
            }

            .app-service-label {
                font-size: 0.72rem;
            }
        }
    </style>

    <!-- Welcome Hero Banner -->
    <div style="margin-top: 1rem; margin-bottom: 2rem;">
        <div class="card"
            style="background: linear-gradient(135deg, #0b1329 0%, #1e293b 60%, #1d4ed8 100%); color: #ffffff; padding: 1.5rem 1.75rem; border-radius: var(--radius-lg); border: 1px solid rgba(255,255,255,0.1); box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : asset('images/default-avatar.svg') }}"
                        alt="Profile Photo"
                        style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2.5px solid rgba(255,255,255,0.8); box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
                        onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                    <div>
                        <h1
                            style="font-size: 1.25rem; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.02em;">
                            Welcome back, Kuya {{ auth()->user()->fullname }}!
                        </h1>
                        <p style="margin: 0.2rem 0 0 0; color: #94a3b8; font-size: 0.85rem; font-weight: 500;">
                            {{ auth()->user()->club->name ?? 'CaragaDos Eagles Club' }} &bull;
                            {{ auth()->user()->region->name ?? 'Region XIII' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- App Services & Tools (Square UI Mobile App Cards) -->
    <div style="margin-bottom: 2.5rem;">
        <h2
            style="font-size: 0.85rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            App Services & Quick Tools
        </h2>

        <div class="app-services-grid" id="mainServicesGrid">

            <!-- Member Mapping -->
            <a href="{{ route('profile.location') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="app-service-label">Member Mapping</span>
            </a>

            <!-- Alalayang Agila Help -->
            <a href="{{ route('quick.response') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="app-service-label">Emergency Help</span>
            </a>

            <!-- Find A Kuya -->
            <a href="{{ route('search.kuya') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <span class="app-service-label">Find A Kuya</span>
            </a>

            <!-- Chat with Kuya -->
            @if(\App\Models\AppSetting::isChatWithKuyaEnabled())
                <a href="{{ route('chat.index') }}" class="app-service-card">
                    <div class="app-service-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <span class="app-service-label">Chat with Kuya</span>
                </a>
            @endif

            <!-- Announcements -->
            <a href="{{ route('announcements.index') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #f59e0b, #b45309);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                        </path>
                    </svg>
                </div>
                <span class="app-service-label">Announcements</span>
            </a>

            <!-- Organizational Structure -->
            <a href="{{ route('org.structure') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #14b8a6, #0f766e);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <span class="app-service-label">Organizational Structure</span>
            </a>

            <!-- Add App to Home Screen -->
            <div onclick="document.getElementById('homeScreenGuideModal').style.display='flex'" class="app-service-card"
                style="cursor: pointer;">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #06b6d4, #0e7490);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="app-service-label">Adding APP in Home Screen</span>
            </div>

            <!-- Libraries Card -->
            @if($canViewLibraries || auth()->user()->is_admin)
                <a href="{{ route('libraries.index') }}" class="app-service-card">
                    <div class="app-service-icon" style="background: linear-gradient(135deg, #8b5cf6, #4c1d95);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <span class="app-service-label">Libraries</span>
                </a>
            @endif

            <!-- Administration Card (Interactive Sub-menu toggle) -->
            @if(auth()->user()->is_admin || auth()->user()->hasPermission('users', 'view'))
                <div onclick="toggleAdminServices(true)" class="app-service-card" style="cursor: pointer;">
                    <div class="app-service-icon" style="background: linear-gradient(135deg, #ec4899, #be185d);">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span class="app-service-label">Administration</span>
                </div>
            @endif

            <!-- Telegram & Push Notification Setup (LAST CARD) -->
            <a href="{{ route('notifications.setup') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #0088cc, #005588);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </div>
                <span class="app-service-label">Telegram & Push Setup</span>
            </a>

        </div>

        <!-- Administration Sub-Services Card Grid -->
        <div class="app-services-grid" id="adminServicesGrid" style="display: none;">

            <!-- Back to Main Services Card -->
            <div onclick="toggleAdminServices(false)" class="app-service-card" style="cursor: pointer; border-color: rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.05);">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #64748b, #334155);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </div>
                <span class="app-service-label" style="font-weight: 700; color: var(--accent);">Back to Services</span>
            </div>

            <!-- Users Management -->
            <a href="{{ route('users.index') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="app-service-label">Users Management</span>
            </a>

            <!-- Access Types -->
            <a href="{{ route('access_types.index') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #8b5cf6, #5b21b6);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <span class="app-service-label">Access Types</span>
            </a>

            <!-- Settings -->
            <a href="{{ route('settings.index') }}" class="app-service-card">
                <div class="app-service-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="app-service-label">Settings</span>
            </a>

        </div>
    </div>

    <!-- Latest Announcements Section (Tabs) -->
    <div style="margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2
                style="font-size: 0.85rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0;">
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
        </div>

        <div class="tab-content">
            <!-- Global Tab -->
            <div id="tab-global" class="announcement-tab-pane active">
                <div class="announcement-stack">
                    @forelse($global_announcements->take(3) as $announcement)
                        @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Global', 'canOpenAnnouncements' => $canAccessAnnouncementsModule])
                    @empty
                        <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                                style="margin-bottom: 1rem; opacity: 0.3;">
                                <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10">
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
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                                style="margin-bottom: 1rem; opacity: 0.3;">
                                <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10">
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
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                                style="margin-bottom: 1rem; opacity: 0.3;">
                                <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10">
                                </path>
                            </svg>
                            <p style="color: var(--text-muted); font-size: 1rem;">No club announcements published yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
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



    <!-- How to Add App to Home Screen Modal -->
    <div id="homeScreenGuideModal"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); align-items: center; justify-content: center; z-index: 1000; padding: 1.25rem; backdrop-filter: blur(6px);">
        <div class="card"
            style="width: 100%; max-width: 760px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg); background: var(--card-bg);">

            <!-- Header -->
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
                                    d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        How to Add App to Home Screen (iOS & Android)
                    </h3>
                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">
                        Install the CaragaDos EC App on your mobile phone for 1-tap instant access.
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('homeScreenGuideModal').style.display='none'"
                    style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--text-muted); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                    onmouseout="this.style.background='none'">&times;</button>
            </div>

            <!-- Fixed OS Selector Tabs Bar -->
            <div
                style="padding: 0.85rem 1.5rem; border-bottom: 1px solid var(--border-color); background: var(--card-bg); flex-shrink: 0;">
                <div
                    style="display: flex; gap: 8px; background: rgba(0,0,0,0.05); padding: 4px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <button type="button" id="tabBtnIos" onclick="switchHomeScreenTab('ios')"
                        style="flex: 1; padding: 0.65rem; font-weight: 700; font-size: 0.88rem; border: none; border-radius: var(--radius-sm); cursor: pointer; background: var(--card-bg); color: var(--accent); box-shadow: var(--shadow-sm);">
                        🍎 iPhone / iPad (iOS)
                    </button>
                    <button type="button" id="tabBtnAndroid" onclick="switchHomeScreenTab('android')"
                        style="flex: 1; padding: 0.65rem; font-weight: 700; font-size: 0.88rem; border: none; border-radius: var(--radius-sm); cursor: pointer; background: transparent; color: var(--text-muted);">
                        🤖 Android (Chrome)
                    </button>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div class="card-body"
                style="overflow-y: auto; padding: 1.5rem; line-height: 1.65; font-size: 0.95rem; color: var(--text-main); display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- iOS Content -->
                <div id="paneIosGuide" style="display: flex; flex-direction: column; gap: 1.25rem;">

                    <!-- Step 1 Card -->
                    <div
                        style="background: rgba(59, 130, 246, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                1</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">Open Safari
                                Browser</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Open <strong>Safari</strong> on your iPhone or iPad and navigate to <strong
                                style="color: var(--accent);">https://app.tfoe-alalayangagila.org/</strong>. <br> Tap
                            the 3 dots button below.
                        </p>
                        <img src="{{ asset('storage/ios/1.jfif') }}" alt="iOS Step 1 - Open Safari Browser"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 2 Card -->
                    <div
                        style="background: rgba(59, 130, 246, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                2</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">Tap the Share
                                Button</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap the <strong>Share icon</strong> <span
                                style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: rgba(59,130,246,0.15); border-radius: 4px; font-weight: 700; color: var(--accent);">⎋</span>
                            (the square icon with an arrow pointing up on Safari's bottom toolbar).
                        </p>
                        <img src="{{ asset('storage/ios/2.jfif') }}" alt="iOS Step 2 - Tap Share Button"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 3 Card -->
                    <div
                        style="background: rgba(59, 130, 246, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                3</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">Tap "View
                                More"</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Scroll down or tap <strong>View More</strong>
                        </p>
                        <img src="{{ asset('storage/ios/3.jfif') }}" alt="iOS Step 3 - Select Add to Home Screen"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 4 Card -->
                    <div
                        style="background: rgba(59, 130, 246, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                4</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">Tap "Add to Home
                                Screen"
                                "Add"</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap <strong>"Add to Home Screen"</strong>
                        </p>
                        <img src="{{ asset('storage/ios/4.jfif') }}" alt="iOS Step 4 - Confirm and Tap Add"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 5 Card -->
                    <div
                        style="background: rgba(59, 130, 246, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                5</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">Rename label
                                Home Screen</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Rename the label of the icon in your iPhone Home Screen and Tap <strong>Add</strong> button
                        </p>
                        <img src="{{ asset('storage/ios/5.jfif') }}" alt="iOS Step 5 - View App Icon on Home Screen"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 6 Card -->
                    <div
                        style="background: rgba(59, 130, 246, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                6</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">Launch App with
                                1-Tap Access</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap the Home Screen icon anytime for instant, full-screen access to CaragaDos EC.
                        </p>
                        <img src="{{ asset('storage/ios/6.jfif') }}" alt="iOS Step 6 - Launch App with 1-Tap Access"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                </div>

                <!-- Android Content -->
                <div id="paneAndroidGuide" style="display: none; flex-direction: column; gap: 1.25rem;">

                    <!-- Step 1 Card -->
                    <div
                        style="background: rgba(34, 197, 94, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #22c55e, #15803d); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                1</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--success);">Open Chrome
                                Browser</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Open <strong>Google Chrome</strong> on your Android phone and navigate to <strong
                                style="color: var(--success);">https://app.tfoe-alalayangagila.org/</strong>. <br>Tap 3
                            dots
                            button in the top right
                        </p>
                        <img src="{{ asset('storage/android/1.jfif') }}" alt="Android Step 1 - Open Chrome Browser"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 2 Card -->
                    <div
                        style="background: rgba(34, 197, 94, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #22c55e, #15803d); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                2</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--success);">Tap
                                <strong>"Install and create shortcut"</strong></span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap the <strong>Install and create shortcut</strong> option from the menu
                        </p>
                        <img src="{{ asset('storage/android/2.jfif') }}" alt="Android Step 2 - Tap Three Dots Menu"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 3 Card -->
                    <div
                        style="background: rgba(34, 197, 94, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #22c55e, #15803d); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                3</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--success);">TA "Install or
                                Create Shortcut"</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap <strong>"Install"</strong> or <strong>"Create Shortcut"</strong> from the menu options.
                        </p>
                        <img src="{{ asset('storage/android/3.jfif') }}" alt="Android Step 3 - Select Add to Home Screen"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 4 Card -->
                    <div
                        style="background: rgba(34, 197, 94, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #22c55e, #15803d); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                4</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--success);">Tap "Install"
                            </span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap <strong>"Install"</strong>.
                        </p>
                        <img src="{{ asset('storage/android/4.jfif') }}" alt="Android Step 4 - Confirm and Tap Add"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 5 Card -->
                    <div
                        style="background: rgba(34, 197, 94, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #22c55e, #15803d); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                5</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--success);">View App Icon on
                                Home Screen</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            The <strong>CaragaDos EC</strong> app shortcut icon is placed on your Android phone's Home
                            Screen.
                        </p>
                        <img src="{{ asset('storage/android/5.jfif') }}" alt="Android Step 5 - View App Icon on Home Screen"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                    <!-- Step 6 Card -->
                    <div
                        style="background: rgba(34, 197, 94, 0.04); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span
                                style="font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #22c55e, #15803d); padding: 0.3rem 0.85rem; border-radius: var(--radius-md); text-transform: uppercase; letter-spacing: 0.05em;">Step
                                6</span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--success);">Launch App with
                                1-Tap Access</span>
                        </div>
                        <p
                            style="font-size: 0.9rem; color: var(--text-main); margin: 0 0 1rem 0; line-height: 1.5; width: 100%; text-align: left;">
                            Tap the Home Screen icon anytime for instant, standalone full-screen access to CaragaDos EC.
                        </p>
                        <img src="{{ asset('storage/android/6.jfif') }}" alt="Android Step 6 - Launch App with 1-Tap Access"
                            style="max-width: 320px; width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); display: block;">
                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="card-footer"
                style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; background: var(--card-bg);">
                <button type="button" onclick="document.getElementById('homeScreenGuideModal').style.display='none'"
                    class="btn btn-primary" style="padding: 0.5rem 1.5rem; font-weight: 600;">Got It, Close</button>
            </div>
        </div>
    </div>

    <script>
        function switchHomeScreenTab(os) {
            const btnIos = document.getElementById('tabBtnIos');
            const btnAndroid = document.getElementById('tabBtnAndroid');
            const paneIos = document.getElementById('paneIosGuide');
            const paneAndroid = document.getElementById('paneAndroidGuide');

            if (os === 'android') {
                btnAndroid.style.background = 'var(--card-bg)';
                btnAndroid.style.color = 'var(--success)';
                btnAndroid.style.boxShadow = 'var(--shadow-sm)';

                btnIos.style.background = 'transparent';
                btnIos.style.color = 'var(--text-muted)';
                btnIos.style.boxShadow = 'none';

                paneAndroid.style.display = 'flex';
                paneIos.style.display = 'none';
            } else {
                btnIos.style.background = 'var(--card-bg)';
                btnIos.style.color = 'var(--accent)';
                btnIos.style.boxShadow = 'var(--shadow-sm)';

                btnAndroid.style.background = 'transparent';
                btnAndroid.style.color = 'var(--text-muted)';
                btnAndroid.style.boxShadow = 'none';

                paneIos.style.display = 'flex';
                paneAndroid.style.display = 'none';
            }
        }
    </script>

    <!-- Persistent Login Credentials & Cookie Consent Modal -->
    <div id="cookieConsentModal"
        style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); align-items: center; justify-content: center; z-index: 1050; padding: 1.25rem; backdrop-filter: blur(8px);">
        <div class="card"
            style="width: 100%; max-width: 520px; border-radius: var(--radius-lg); border: 1px solid rgba(59, 130, 246, 0.3); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); background: var(--card-bg); overflow: hidden; animation: cookieModalFadeIn 0.3s ease-out;">

            <!-- Header -->
            <div
                style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.05)); padding: 1.5rem 1.75rem 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); position: relative;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div
                        style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4); flex-shrink: 0;">
                        🍪
                    </div>
                    <div>
                        <h3
                            style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0 0 0.2rem 0; letter-spacing: -0.01em;">
                            Persistent Login & Cookie Notice
                        </h3>
                        <span
                            style="font-size: 0.8rem; color: var(--accent); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                            Keep Your Session Active
                        </span>
                    </div>
                </div>
                <button type="button" onclick="closeCookieModal()"
                    style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--text-muted); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                    onmouseout="this.style.background='none'">&times;</button>
            </div>

            <!-- Body -->
            <div style="padding: 1.5rem 1.75rem; font-size: 0.9rem; color: var(--text-main); line-height: 1.6;">
                <p style="margin: 0 0 1.25rem 0; color: var(--text-muted);">
                    We use persistent authentication cookies to keep you signed in on this device. By accepting,
                    <strong>your login credentials will never expire automatically</strong>, giving you instant access
                    anytime.
                </p>

                <div
                    style="display: flex; flex-direction: column; gap: 0.85rem; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: var(--radius-md); padding: 1rem;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            style="color: var(--accent); flex-shrink: 0; margin-top: 2px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        <div>
                            <strong style="font-size: 0.85rem; color: var(--text-main); display: block;">No Repeated
                                Logins Needed</strong>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Your session stays remembered
                                safely in browser cookies.</span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            style="color: var(--success); flex-shrink: 0; margin-top: 2px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        <div>
                            <strong style="font-size: 0.85rem; color: var(--text-main); display: block;">Encrypted
                                Security Token</strong>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Protected via secure HTTPS
                                authentication standards.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="padding: 1rem 1.75rem 1.5rem 1.75rem; border-top: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.75rem; background: var(--card-bg);">
                <button type="button" onclick="acceptCookieRemember()" class="btn btn-primary"
                    style="width: 100%; justify-content: center; padding: 0.7rem 1.25rem; font-weight: 700; font-size: 0.9rem; border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        style="margin-right: 6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Accept & Remember My Login Credentials
                </button>
                <button type="button" onclick="closeCookieModal()" class="btn btn-outline"
                    style="width: 100%; justify-content: center; padding: 0.5rem 1rem; font-size: 0.82rem; font-weight: 600; color: var(--text-muted); border-color: var(--border-color);">
                    Close Notice
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleAdminServices(showAdmin) {
            const mainGrid = document.getElementById('mainServicesGrid');
            const adminGrid = document.getElementById('adminServicesGrid');
            if (mainGrid && adminGrid) {
                if (showAdmin) {
                    mainGrid.style.display = 'none';
                    adminGrid.style.display = 'grid';
                } else {
                    adminGrid.style.display = 'none';
                    mainGrid.style.display = 'grid';
                }
            }
        }

        function closeCookieModal() {
            const modal = document.getElementById('cookieConsentModal');
            if (modal) modal.style.display = 'none';
        }

        function acceptCookieRemember() {
            localStorage.setItem('cookie_remember_accepted', 'true');
            document.cookie = "cookie_remember_accepted=true; max-age=315360000; path=/; SameSite=Lax";

            fetch("{{ route('accept.cookie.remember') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }).catch(err => console.log(err));

            closeCookieModal();
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(request('view') === 'admin')
                toggleAdminServices(true);
            @endif

            @if(session('show_cookie_popup') || session('from_login'))
                const cookieModal = document.getElementById('cookieConsentModal');
                if (cookieModal) {
                    cookieModal.style.display = 'flex';
                }
            @else
                if (!localStorage.getItem('cookie_remember_accepted')) {
                    const cookieModal = document.getElementById('cookieConsentModal');
                    if (cookieModal) {
                        cookieModal.style.display = 'flex';
                    }
                }
            @endif
        });
    </script>
@endsection