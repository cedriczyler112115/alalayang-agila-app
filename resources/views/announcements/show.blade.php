@extends('layouts.app')

@section('title', $announcement->title . ' - Caragados EC')

@section('content')
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
                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Kuya {{ $announcement->user->fullname }}</span>
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
    <div class="card" style="padding: 2rem; margin-bottom: 3rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Comments</h3>
        
        <!-- Root Comment Form -->
        <form action="{{ route('comments.store', $announcement) }}" method="POST" style="margin-bottom: 2rem;">
            @csrf
            <div style="display: flex; gap: 1rem; align-items: flex-start;">
                <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : asset('images/default-avatar.svg') }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;" onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                <div style="flex: 1;">
                    <textarea name="content" class="form-control" rows="3" placeholder="Join the discussion..." required style="resize: vertical;"></textarea>
                    <div style="margin-top: 0.5rem; text-align: right;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Post Comment</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Comments List -->
        <div class="comments-list">
            @forelse($announcement->comments()->whereNull('parent_id')->latest()->get() as $comment)
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
                form.querySelector('input[name="content"]').focus();
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
