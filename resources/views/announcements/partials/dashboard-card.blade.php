<div class="announcement-card">
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
        @if($badge === 'Global')
            <img src="{{ asset('storage/eaglelogo.png') }}" alt="Global Logo" style="width: 42px; height: 42px; border-radius: 50%; object-fit: contain; border: 1px solid var(--border-color);" onerror="this.style.display='none'">
        @elseif($badge === 'Regional')
            @php $regionLogo = auth()->user()->region && auth()->user()->region->logo ? asset('storage/' . auth()->user()->region->logo) : ''; @endphp
            @if($regionLogo)
                <img src="{{ $regionLogo }}" alt="Regional Logo" style="width: 42px; height: 42px; border-radius: 50%; object-fit: contain; border: 1px solid var(--border-color);" onerror="this.style.display='none'">
            @endif
        @elseif($badge === 'Club')
            @php $clubLogo = auth()->user()->club && auth()->user()->club->logo ? asset('storage/' . auth()->user()->club->logo) : ''; @endphp
            @if($clubLogo)
                <img src="{{ $clubLogo }}" alt="Club Logo" style="width: 42px; height: 42px; border-radius: 50%; object-fit: contain; border: 1px solid var(--border-color);" onerror="this.style.display='none'">
            @endif
        @endif
        <div class="announcement-badge" style="margin-bottom: 0;">{{ $badge }} Announcement</div>
    </div>
    <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; line-height: 1.2;">{{ $announcement->title }}</h3>
    
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        @php
            $authorPhoto = $announcement->user && $announcement->user->profile_photo
                ? asset('storage/' . $announcement->user->profile_photo)
                : asset('images/default-avatar.svg');
        @endphp
        <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.85rem;">
            <img src="{{ $authorPhoto }}" alt="{{ $announcement->user->fullname ?? 'Author' }}"
                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);"
                onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
            <span style="font-weight: 600; color: var(--text-main);">Kuya {{ $announcement->user->fullname ?? 'Unknown' }}</span>
        </div>
        <div style="width: 4px; height: 4px; border-radius: 50%; background-color: var(--border-color);"></div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>{{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : 'Unknown Date' }} ({{ $announcement->published_at ? $announcement->published_at->diffForHumans() : '' }})</span>
        </div>
    </div>

    <div class="ck-content" style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 0.5rem; max-height: 150px; overflow: hidden; position: relative;">
        {!! $announcement->content !!}
        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 40px; background: linear-gradient(transparent, var(--card-bg));"></div>
    </div>
    <div style="margin-top: 0.5rem;">
        <a href="{{ route('announcements.show', $announcement) }}" style="color: var(--accent); font-weight: 600; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 4px;">
            Read More 
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</div>
