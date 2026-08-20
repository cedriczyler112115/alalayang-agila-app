@extends('layouts.app')

@section('title', $announcement->title . ' - Caragados EC')

@section('content')
@php
    $rootComments = $announcement->comments;
@endphp
<div style="margin-top: 2rem;">
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="margin-right: 1rem; padding: 0.5rem; border-radius: 50%;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; letter-spacing: -0.025em;">{{ $announcement->title }}</h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                @php
                    $authorPhoto = $announcement->user && $announcement->user->profile_photo
                        ? asset('storage/' . $announcement->user->profile_photo)
                        : asset('images/default-avatar.svg');
                    $userUrl = $announcement->user_id ? route('members.show', $announcement->user_id) : '#';
                @endphp
                <div style="display: flex; align-items: center; gap: 0.65rem; font-size: 0.9rem;">
                    <a href="{{ $userUrl }}" class="author-profile-link"
                        data-user-name="Kuya {{ $announcement->user->fullname ?? 'Unknown' }}"
                        data-user-photo="{{ $authorPhoto }}"
                        data-user-position="{{ $announcement->user->position->name ?? 'Club Member' }}"
                        data-user-club="{{ $announcement->user->club->name ?? 'No Club Specified' }}"
                        data-user-region="{{ $announcement->user->region->name ?? 'No Region Specified' }}"
                        data-user-address="{{ $announcement->user->address ?? 'No Address Listed' }}"
                        style="display: inline-flex; text-decoration: none;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        <img src="{{ $authorPhoto }}" alt="{{ $announcement->user->fullname ?? 'Author' }}"
                            style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);"
                            onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                    </a>
                    <a href="{{ $userUrl }}"
                        style="font-weight: 600; color: var(--text-main); text-decoration: none;"
                        onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'">
                        Kuya {{ $announcement->user->fullname ?? 'Unknown' }}
                    </a>
                </div>
                <div style="width: 4px; height: 4px; border-radius: 50%; background-color: var(--border-color);"></div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $announcement->published_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 3rem; margin-bottom: 3rem;">
        <div class="ck-content">
            {!! $announcement->content !!}
        </div>

    </div>

    <!-- Comments Section -->
    <div class="card" id="announcement-comments" style="padding: 2rem; margin-bottom: 1rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Comments</h3>

        @if(session('status'))
            <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger); padding: 1rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        
        <!-- Root Comment Form -->
        <form action="{{ route('comments.store', $announcement) }}" method="POST" style="margin-bottom: 2rem;">
            @csrf
            <div style="display: flex; gap: 1rem; align-items: flex-start;">
                @php
                    $authUserPhoto = auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : asset('images/default-avatar.svg');
                    $authUserUrl = route('members.show', auth()->id());
                @endphp
                <a href="{{ $authUserUrl }}" class="author-profile-link"
                    data-user-name="Kuya {{ auth()->user()->fullname }}"
                    data-user-photo="{{ $authUserPhoto }}"
                    data-user-position="{{ auth()->user()->position->name ?? 'Club Member' }}"
                    data-user-club="{{ auth()->user()->club->name ?? 'No Club Specified' }}"
                    data-user-region="{{ auth()->user()->region->name ?? 'No Region Specified' }}"
                    data-user-address="{{ auth()->user()->address ?? 'No Address Listed' }}"
                    style="display: inline-flex; flex-shrink: 0; text-decoration: none;"
                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    <img src="{{ $authUserPhoto }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);" onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                </a>
                <div style="flex: 1;">
                    <textarea name="content" class="form-control" rows="3" placeholder="Join the discussion..." required style="resize: vertical;">{{ old('parent_id') ? '' : old('content') }}</textarea>
                    <div style="margin-top: 0.5rem; text-align: right;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Post Comment</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Comments List -->
        <div class="comments-list">
            @forelse($rootComments as $comment)
                @include('announcements.partials.comment', ['comment' => $comment, 'announcement' => $announcement])
            @empty
                <p style="text-align: center; color: var(--text-muted); font-style: italic; margin-top: 2rem;">No comments yet. Be the first to start the conversation!</p>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('reply-btn') || e.target.closest('.reply-btn')) {
            let btn = e.target.closest('.reply-btn');
            let id = btn.getAttribute('data-id');
            let form = document.getElementById('reply-form-' + id);
            
            if(form.style.display === 'none') {
                form.style.display = 'block';
                form.querySelector('textarea[name="content"]').focus();
            } else {
                form.style.display = 'none';
            }
        }
    });
});
</script>

<style>
    /* CKEditor Content Rendering Styles */
    .ck-content img {
        max-width: 100%;
        height: auto !important;
        display: block;
        margin: 2rem auto;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
    }
    .ck-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
    }
    .ck-content table td, .ck-content table th {
        border: 1px solid var(--border-color);
        padding: 1rem;
    }
    .ck-content blockquote {
        border-left: 5px solid var(--accent);
        padding: 1rem 2rem;
        margin: 2rem 0;
        font-style: italic;
        color: var(--text-muted);
        background-color: rgba(0,0,0,0.02);
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
    }
    .ck-content p {
        margin-bottom: 1.5rem;
        line-height: 1.8;
        font-size: 1.1rem;
    }
    .ck-content h1, .ck-content h2, .ck-content h3, .ck-content h4 {
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: var(--text-main);
    }
</style>
@endsection
