<div class="comment" style="margin-top: 1rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background-color: var(--card-bg);">
    <div style="display: flex; gap: 1rem; align-items: flex-start;">
        <img src="{{ $comment->user->profile_photo ? asset('storage/' . $comment->user->profile_photo) : asset('images/default-avatar.png') }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" onerror="this.src='{{ asset('images/default-avatar.png') }}'">
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                <span style="font-weight: 700; color: var(--text-main);">Kuya {{ $comment->user->fullname }}</span>
                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p style="margin-bottom: 0.75rem; color: var(--text-muted); line-height: 1.5; font-size: 0.95rem;">
                {{ $comment->content }}
            </p>
            
            <button class="btn btn-outline reply-btn" data-id="{{ $comment->id }}" style="padding: 0.2rem 0.5rem; font-size: 0.8rem; border-color: transparent; color: var(--accent);">
                Reply
            </button>

            <!-- Reply Form Hidden -->
            <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none; margin-top: 1rem;">
                <form action="{{ route('comments.store', $announcement) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="content" class="form-control" placeholder="Write a reply..." required style="flex: 1; min-height: 38px;">
                        <button type="submit" class="btn btn-primary" style="padding: 0 1rem; border-radius: var(--radius-md);">Submit</button>
                    </div>
                </form>
            </div>

            <!-- Render Nested Replies Recursively -->
            @if($comment->replies->count() > 0)
                <div class="replies" style="margin-top: 1rem; border-left: 2px solid var(--border-color); padding-left: 1rem;">
                    @foreach($comment->replies as $reply)
                        @include('announcements.partials.comment', ['comment' => $reply, 'announcement' => $announcement])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
