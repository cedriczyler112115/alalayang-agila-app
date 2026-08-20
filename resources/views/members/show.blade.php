@extends('layouts.app')

@section('title', ($user->fullname ?? 'Member Profile') . ' - Caragados EC')

@section('content')
<!-- Leaflet CSS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div style="margin-top: 2rem; margin-bottom: 3rem;">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="javascript:history.back()" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <!-- Main Profile Card -->
    <div class="card" style="border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-lg);">
        
        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #3b82f6 100%); padding: 2.5rem 2rem 2rem 2rem; color: #ffffff; position: relative;">
            <div style="display: flex; align-items: center; gap: 1.75rem; flex-wrap: wrap;">
                @php
                    $photoUrl = $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.svg');
                @endphp
                <img src="{{ $photoUrl }}" alt="{{ $user->fullname }}"
                    title="Click to view full image"
                    onclick="openImagePopup('{{ $photoUrl }}', '{{ $user->fullname }} Profile Photo')"
                    style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.9); box-shadow: 0 8px 20px rgba(0,0,0,0.3); cursor: pointer; transition: transform 0.2s;"
                    onmouseover="this.style.transform='scale(1.05)';"
                    onmouseout="this.style.transform='scale(1)';"
                    onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                
                <div style="flex: 1; min-width: 240px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.35rem; flex-wrap: wrap;">
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.02em;">
                            Kuya {{ $user->fullname }}
                        </h1>
                        @if($user->status === 1)
                            <span style="background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80; padding: 2px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Active Eagle</span>
                        @endif
                    </div>

                    <p style="margin: 0 0 0.75rem 0; font-size: 1rem; color: #94a3b8; font-weight: 600;">
                        {{ $user->position->name ?? 'Club Member' }}
                    </p>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.88rem; color: #cbd5e1;">
                        @if($user->club)
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>{{ $user->club->name }}</span>
                            </div>
                        @endif
                        @if($user->region)
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span>{{ $user->region->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Details -->
        <div class="card-body" style="padding: 2rem;">
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;" id="profile-grid">
                
                <!-- Left Column: Personal Information -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px;">
                            👤 Personal & Membership Info
                        </h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                        <div>
                            <span style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px;">Sex</span>
                            <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">{{ $user->sex ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <span style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px;">Marital Status</span>
                            <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">{{ $user->marital_status ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <span style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px;">Occupation / Job</span>
                            <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">{{ $user->current_job ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <span style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px;">Office / Company</span>
                            <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">{{ $user->office ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px;">Address</span>
                        <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">{{ $user->address ?? 'No address listed' }}</span>
                    </div>

                    <!-- Eagle Identification Card Section -->
                    @if($user->eagle_id_card)
                        <div style="margin-top: 1rem;">
                            <span style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Eagle Identification Card</span>
                            @php
                                $eagleUrl = asset('storage/' . $user->eagle_id_card);
                            @endphp
                            <img src="{{ $eagleUrl }}" alt="Eagle ID Card"
                                title="Click to view full image"
                                onclick="openImagePopup('{{ $eagleUrl }}', '{{ $user->fullname }} Eagle ID Card')"
                                style="max-width: 320px; width: 100%; height: 180px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); cursor: pointer; transition: transform 0.2s;"
                                onmouseover="this.style.transform='scale(1.03)';"
                                onmouseout="this.style.transform='scale(1)';"
                                onerror="this.style.display='none'">
                        </div>
                    @endif

                </div>

                <!-- Right Column: Tagged Map Location -->
                @if($user->location && str_contains($user->location, ','))
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px;">
                                📍 Tagged Address Location
                            </h3>
                        </div>
                        <div id="memberMap" style="height: 350px; width: 100%; border-radius: var(--radius-md); border: 1px solid var(--border-color);"></div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" onclick="closeImagePopup(event)"
    style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); align-items: center; justify-content: center; z-index: 2000; padding: 1.5rem; backdrop-filter: blur(8px);">
    <div style="position: relative; max-width: 90vw; max-height: 90vh; display: flex; flex-direction: column; align-items: center; background: var(--card-bg); padding: 1rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);" onclick="event.stopPropagation();">
        <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; color: var(--text-main);">
            <h4 id="imageModalTitle" style="margin: 0; font-size: 1.05rem; font-weight: 700;">Full View</h4>
            <button type="button" onclick="closeImagePopup()"
                style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--text-muted); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                onmouseout="this.style.background='none'">&times;</button>
        </div>
        <img id="imageModalSrc" src="" alt="Preview"
            style="max-width: 85vw; max-height: 75vh; object-fit: contain; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: block;">
    </div>
</div>

<script>
    function openImagePopup(src, title) {
        if (!src) return;
        document.getElementById('imageModalSrc').src = src;
        document.getElementById('imageModalTitle').textContent = title || 'Image Preview';
        document.getElementById('imagePreviewModal').style.display = 'flex';
    }

    function closeImagePopup(event) {
        if (!event || event.target.id === 'imagePreviewModal' || event.target.tagName === 'BUTTON') {
            document.getElementById('imagePreviewModal').style.display = 'none';
        }
    }

    @if($user->location && str_contains($user->location, ','))
    document.addEventListener('DOMContentLoaded', function() {
        const coords = "{{ $user->location }}".split(',');
        const lat = parseFloat(coords[0]);
        const lng = parseFloat(coords[1]);

        if (!isNaN(lat) && !isNaN(lng)) {
            const map = L.map('memberMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>Kuya {{ $user->fullname }}</b><br>{{ $user->address }}")
                .openPopup();
        }
    });
    @endif
</script>
@endsection
