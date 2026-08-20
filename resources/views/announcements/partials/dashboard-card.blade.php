<div class="announcement-card" style="border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg); padding: 1.25rem; transition: transform 0.2s ease, box-shadow 0.2s ease; box-shadow: var(--shadow-sm); margin-bottom: 1.25rem;">
    <!-- Mobile App Feed Post Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
        @php
            $authorPhoto = $announcement->user && $announcement->user->profile_photo
                ? asset('storage/' . $announcement->user->profile_photo)
                : asset('images/default-avatar.svg');
            $userUrl = $announcement->user_id ? route('members.show', $announcement->user_id) : '#';
        @endphp
        
        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
            <a href="{{ $userUrl }}" style="display: inline-flex; text-decoration: none; flex-shrink: 0;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <img src="{{ $authorPhoto }}" alt="{{ $announcement->user->fullname ?? 'Author' }}"
                    style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); shadow: 0 2px 8px rgba(0,0,0,0.15);"
                    onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
            </a>
            <div style="overflow: hidden;">
                <a href="{{ $userUrl }}"
                    style="font-weight: 700; color: var(--text-main); text-decoration: none; font-size: 0.95rem; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                    onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'">
                    Kuya {{ $announcement->user->fullname ?? 'Unknown' }}
                </a>
                <div style="font-size: 0.78rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span>{{ $announcement->user->club->name ?? 'CaragaDos Club' }}</span>
                    <span>&bull;</span>
                    <span>{{ $announcement->published_at ? $announcement->published_at->diffForHumans() : 'Recently' }}</span>
                </div>
            </div>
        </div>

        <div style="flex-shrink: 0;">
            @if($badge === 'Global')
                <span style="background: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;">
                    🌐 Global
                </span>
            @elseif($badge === 'Regional')
                <span style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;">
                    🏛️ Regional
                </span>
            @elseif($badge === 'Club')
                <span style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;">
                    🦅 My Club
                </span>
            @endif
        </div>
    </div>

    <!-- Announcement Title & Content -->
    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.6rem; line-height: 1.35; letter-spacing: -0.01em;">
        <a href="{{ route('announcements.show', $announcement) }}" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'">
            {{ $announcement->title }}
        </a>
    </h3>

    <div class="ck-content" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 0.85rem; max-height: 120px; overflow: hidden; position: relative;">
        {!! $announcement->content !!}
        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 35px; background: linear-gradient(transparent, var(--card-bg));"></div>
    </div>
    
    <!-- Footer Mobile App Action Bar -->
    <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.78rem; color: var(--text-muted); font-weight: 500;">
            📅 {{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : '' }}
        </span>
        <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-outline btn-sm"
            style="color: var(--accent); border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.08); font-weight: 700; font-size: 0.8rem; border-radius: 9999px; padding: 0.4rem 1.1rem; text-decoration: none;">
            Read Story &rarr;
        </a>
    </div>
</div>
