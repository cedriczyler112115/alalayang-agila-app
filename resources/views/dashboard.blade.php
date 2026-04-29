@extends('layouts.app')

@section('title', 'Dashboard - Caragados EC')

@section('content')
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
    .ck-content table td, .ck-content table th {
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
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
<div style="margin-top: 2rem;">
    <h1 style="font-size: 1.2rem; font-weight: 500; margin-bottom: 0.5rem; letter-spacing: -0.025em;">Welcome back, <span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem;">Kuya</span> <span style="color: var(--accent);">{{ auth()->user()->fullname }}</span>!</h1>
    <p style="color: var(--text-muted); margin-bottom: 2.5rem; font-size: 1.05rem;">Here is an overview of your Caragados Eagles Club dashboard.</p>

    @if(session('status'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    <!-- Latest Announcements Section (Tabs) -->
    <div style="margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 0.9rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0;">Latest Announcements</h2>
            <a href="{{ route('announcements.index') }}" style="font-size: 0.8rem; color: var(--accent); text-decoration: none; font-weight: 600;">View All Announcements</a>
        </div>
        
        <div class="announcement-tabs" id="announcementTabs">
            <button class="announcement-tab-btn active" data-target="tab-global">Global</button>
            <button class="announcement-tab-btn" data-target="tab-regional">Regional</button>
            <button class="announcement-tab-btn" data-target="tab-club">My Club</button>
        </div>

        <div class="tab-content">
            <!-- Global Tab -->
            <div id="tab-global" class="announcement-tab-pane active">
                <div class="announcement-stack">
                    @forelse($global_announcements->take(3) as $announcement)
                        @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Global'])
                    @empty
                        <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path></svg>
                            <p style="color: var(--text-muted); font-size: 1rem;">No global announcements published yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Regional Tab -->
            <div id="tab-regional" class="announcement-tab-pane">
                <div class="announcement-stack">
                    @forelse($regional_announcements->take(3) as $announcement)
                        @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Regional'])
                    @empty
                        <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path></svg>
                            <p style="color: var(--text-muted); font-size: 1rem;">No regional announcements published yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Club Tab -->
            <div id="tab-club" class="announcement-tab-pane">
                <div class="announcement-stack">
                    @forelse($club_announcements->take(3) as $announcement)
                        @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Club'])
                    @empty
                        <div class="announcement-card" style="text-align: center; padding: 4rem 2rem;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path></svg>
                            <p style="color: var(--text-muted); font-size: 1rem;">No club announcements published yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div style="margin-bottom: 2.5rem;">
        <h2 style="font-size: 0.9rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Quick Links</h2>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            
            <!-- Alalayang Agila Help Card -->
            <a href="{{ route('quick.response') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #f59e0b;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Alalayang Agila Help</h3>
            </a>

            <!-- Group Chat Card -->
            <a href="{{ route('chat.index') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #8b5cf6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Group Chat</h3>
            </a>

            <!-- Search A Kuya Card -->
            <a href="{{ route('search.kuya') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--accent);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Search A Kuya</h3>
            </a>

            <!-- My Profile Card -->
            <a href="{{ route('profile.complete') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--success);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">My Profile</h3>
            </a>

            <!-- Member Mapping Card -->
            <a href="{{ route('profile.location') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(236, 72, 153, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #ec4899;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Member Mapping</h3>
            </a>

            <!-- Organizational Structure Card -->
            <a href="{{ route('org.structure') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(20, 184, 166, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #14b8a6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Org Structure</h3>
            </a>

            <!-- Announcements Card -->
            <a href="{{ route('announcements.index') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--accent);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Announcements</h3>
            </a>

            <!-- Libraries Card -->
            <a href="{{ route('libraries.index') }}" class="card quick-link-card" style="text-decoration: none; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #8b5cf6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Libraries</h3>
            </a>
        </div>
    </div>

    <!-- Profile Overview Card -->
    <div class="card" style="margin-bottom: 2.5rem;">
        <div class="card-header">
            <h3 class="card-title">Profile Overview</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                <div style="flex: 1; min-width: 250px;">
                    <span style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Region</span>
                    <div style="font-weight: 500; font-size: 1.1rem; margin-top: 0.25rem;">{{ auth()->user()->region->name ?? 'Not Set' }}</div>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <span style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Eagle Club Name</span>
                    <div style="font-weight: 500; font-size: 1.1rem; margin-top: 0.25rem;">{{ auth()->user()->club->name ?? 'Not Set' }}</div>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <span style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Contact</span>
                    <div style="font-weight: 500; font-size: 1.1rem; margin-top: 0.25rem;">{{ auth()->user()->contact_number }}</div>
                </div>
                <div style="flex: 2; min-width: 250px;">
                    <span style="font-size: 0.75rem; color: var(--secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Address</span>
                    <div style="font-weight: 500; font-size: 1.1rem; margin-top: 0.25rem;">{{ auth()->user()->address }}</div>
                </div>                    
            </div>
            <div class="mt-6" style="display: flex; gap: 0.75rem; max-width: 400px;">
                <a href="{{ route('profile.complete') }}" class="btn btn-outline" style="flex: 1;">Edit Profile</a>
                <a href="{{ route('profile.location') }}" class="btn btn-outline" style="flex: 1;">Member Mapping</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
@endsection
