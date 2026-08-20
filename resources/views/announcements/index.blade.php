@extends('layouts.app')

@section('title', 'Announcements - Caragados EC')

@section('content')
@php
    $premiumFeatureLockEnabled = \App\Models\AppSetting::isPremiumFeatureLockEnabled();
    $canAddAnnouncements = !$premiumFeatureLockEnabled || auth()->user()->hasPermission('announcements', 'add');
    $canEditAnnouncements = !$premiumFeatureLockEnabled || auth()->user()->hasPermission('announcements', 'edit');
    $canDeleteAnnouncements = !$premiumFeatureLockEnabled || auth()->user()->hasPermission('announcements', 'delete');
@endphp
<div style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.025em;">Manage <span style="color: var(--accent);">Announcements</span></h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">Create and manage announcements for the club members.</p>
        </div>
        @if($canAddAnnouncements)
            <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Create
            </a>
        @endif
    </div>

    @if(session('status'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    <!-- Search/Filter -->
    <div class="card" style="margin-bottom: 2rem; padding: 1.25rem;">
        <form action="{{ route('announcements.index') }}" method="GET" style="display: flex; gap: 1rem;">
            <div style="flex: 1; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search announcements by title or content..." class="form-control" style="padding-left: 2.5rem;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('announcements.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: rgba(0,0,0,0.02); border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">Title</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">Club Name</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">Published Date</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">Author</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(0,0,0,0.01)'" onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 1rem 1.5rem;">
                            <a href="{{ route('announcements.show', $announcement) }}" style="text-decoration: none; display: block;">
                                <div style="font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">{{ $announcement->title }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ strip_tags($announcement->content) }}</div>
                            </a>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
                            {{ $announcement->club->name ?? $announcement->user->club->name ?? 'N/A' }}
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
                            {{ $announcement->published_at ? $announcement->published_at->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            @if($announcement->status === 'published')
                                <span style="background-color: rgba(34, 197, 94, 0.1); color: var(--success); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Published</span>
                            @else
                                <span style="background-color: rgba(100, 116, 139, 0.1); color: var(--text-muted); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Draft</span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
                            @php
                                $authorPhoto = $announcement->user && $announcement->user->profile_photo
                                    ? asset('storage/' . $announcement->user->profile_photo)
                                    : asset('images/default-avatar.svg');
                            @endphp
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <img src="{{ $authorPhoto }}" alt="Author Avatar"
                                    style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--accent);"
                                    onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                                <span>{{ $announcement->user->fullname ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right;">
                            @if($canEditAnnouncements || $canDeleteAnnouncements)
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    @if($canEditAnnouncements)
                                        <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-outline" style="padding: 0.4rem; border-radius: var(--radius-md);" title="Edit Announcement">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($canDeleteAnnouncements)
                                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement? This action cannot be undone.')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline" style="padding: 0.4rem; border-radius: var(--radius-md); color: var(--danger); border-color: rgba(239, 68, 68, 0.2);" title="Delete Announcement">
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.85rem;">No actions</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem 1.5rem; text-align: center; color: var(--text-muted);">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 1rem; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 3v5h5M7 12h10M7 16h10"></path>
                            </svg>
                            <p style="font-size: 1.1rem; font-weight: 500;">No announcements found.</p>
                            @if(request('search'))
                                <p style="font-size: 0.9rem;">Try adjusting your search query.</p>
                            @else
                                <p style="font-size: 0.9rem;">{{ $canAddAnnouncements ? 'Click "Create" to get started.' : 'No announcements are available right now.' }}</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $announcements->links() }}
    </div>
</div>

<style>
    /* Pagination Styling Fixes */
    .pagination {
        display: flex;
        gap: 0.25rem;
        list-style: none;
        padding: 0;
    }
    .page-item .page-link {
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-main);
        text-decoration: none;
        border-radius: var(--radius-md);
        font-weight: 500;
        transition: all 0.2s;
    }
    .page-item.active .page-link {
        background-color: var(--accent);
        border-color: var(--accent);
        color: white;
    }
    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .page-item:not(.active):not(.disabled) .page-link:hover {
        border-color: var(--accent);
        color: var(--accent);
    }
</style>
@endsection
