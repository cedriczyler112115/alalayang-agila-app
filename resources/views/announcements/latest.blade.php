@extends('layouts.app')

@section('title', 'Latest Announcements - Caragados EC')

@section('content')
<style>
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
</style>

<div style="margin-top: 2rem;">
    <!-- Page Header with Back Button -->
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding: 0.5rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;" title="Back to Dashboard">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.025em;">Latest <span style="color: var(--accent);">Announcements</span></h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Stay updated with global, regional, and club announcements.</p>
        </div>
    </div>

    <!-- Segmented Control Tabs -->
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: center;">
        <div class="announcement-tabs" id="announcementTabs">
            <button class="announcement-tab-btn active" data-target="tab-global">Global</button>
            <button class="announcement-tab-btn" data-target="tab-regional">Regional</button>
            <button class="announcement-tab-btn" data-target="tab-club">My Club</button>
        </div>
    </div>

    <!-- Tab Content Panes -->
    <div class="tab-content">
        <!-- Global Tab -->
        <div id="tab-global" class="announcement-tab-pane active">
            <div class="announcement-stack">
                @forelse($global_announcements as $announcement)
                    @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Global'])
                @empty
                    <div class="announcement-card" style="text-align: center; padding: 4rem 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;">
                            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path>
                        </svg>
                        <p style="color: var(--text-muted); font-size: 1rem;">No global announcements published yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Regional Tab -->
        <div id="tab-regional" class="announcement-tab-pane">
            <div class="announcement-stack">
                @forelse($regional_announcements as $announcement)
                    @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Regional'])
                @empty
                    <div class="announcement-card" style="text-align: center; padding: 4rem 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;">
                            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path>
                        </svg>
                        <p style="color: var(--text-muted); font-size: 1rem;">No regional announcements published yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Club Tab -->
        <div id="tab-club" class="announcement-tab-pane">
            <div class="announcement-stack">
                @forelse($club_announcements as $announcement)
                    @include('announcements.partials.dashboard-card', ['announcement' => $announcement, 'badge' => 'Club'])
                @empty
                    <div class="announcement-card" style="text-align: center; padding: 4rem 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg);">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;">
                            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path>
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
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-target');

                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                this.classList.add('active');
                const targetPane = document.getElementById(target);
                if (targetPane) {
                    targetPane.classList.add('active');
                }
            });
        });
    });
</script>
@endsection
