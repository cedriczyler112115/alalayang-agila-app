@extends('layouts.app')

@section('title', 'Member Mapping - Caragados EC')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.025em;"><span style="font-family: 'Brush Script MT', cursive; font-size: 2.5rem; font-weight: 400;">Kuya</span> <span style="color: var(--accent);">Mapping</span></h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">Geotagged locations of all Caragados Eagles Club members.</p>
        </div>
        <div style="background-color: var(--card-bg); padding: 0.75rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <div style="width: 12px; height: 12px; background-color: var(--accent); border-radius: 50%;"></div>
            <span style="font-weight: 600; font-size: 0.9rem;">{{ $members->count() }} Members Tagged</span>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <div id="map" style="height: 600px; width: 100%;max-height: 600px;max-width: 100%;"></div>
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial center (Caraga area)
        let centerLat = 8.9475;
        let centerLng = 125.5406;
        
        // If current user has a location, center on them
        @if(auth()->user()->location)
            const userLoc = "{{ auth()->user()->location }}".split(',');
            centerLat = parseFloat(userLoc[0]);
            centerLng = parseFloat(userLoc[1]);
        @endif

        const map = L.map('map').setView([centerLat, centerLng], 10);

        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EBP, and the GIS User Community'
        });

        const baseMaps = {
            "Standard Map": osm,
            "Satellite View": satellite
        };

        osm.addTo(map);
        L.control.layers(baseMaps).addTo(map);

        // Member Data
        const members = @json($members);
        const storageBase = "{{ asset('storage') }}";
        const logoUrl = "{{ asset('images/logo.png') }}";

        members.forEach(member => {
            if (member.location && member.location.includes(',')) {
                const coords = member.location.split(',');
                const lat = parseFloat(coords[0]);
                const lng = parseFloat(coords[1]);

                const fullName = `${member.last_name}, ${member.first_name} ${member.middle_name || ''} ${member.extension_name || ''}`.trim();
                const clubName = member.club ? member.club.name : 'No Club';
                const regionName = member.region ? member.region.name : 'No Region';
                const photoUrl = member.profile_photo ? `${storageBase}/profile-photos/${member.profile_photo.split('/').pop()}?t=${Date.now()}` : logoUrl;

                const popupContent = `
                    <div style="display: flex; gap: 12px; min-width: 250px; padding: 5px; align-items: center;">
                        <div style="flex-shrink: 0;">
                            <img loading="lazy" src="${photoUrl}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); display: block;" onerror="this.src='${logoUrl}'">
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 1rem; margin-bottom: 2px; line-height: 1.2;"><span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem; font-weight: 400;">Kuya</span> ${fullName}</div>
                            <div style="font-size: 0.8rem; color: var(--accent); font-weight: 600; margin-bottom: 2px;">${clubName}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${regionName}</div>
                        </div>
                    </div>
                `;

                L.marker([lat, lng]).addTo(map)
                    .bindPopup(popupContent);
            }
        });

        // If no members have locations, fit to a default view, otherwise fit bounds
        if (members.length > 0) {
            const group = new L.featureGroup(members.map(m => {
                if (m.location) {
                    const c = m.location.split(',');
                    return L.marker([parseFloat(c[0]), parseFloat(c[1])]);
                }
            }).filter(m => m !== undefined));
            
            if (group.getLayers().length > 0) {
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
    });
</script>

<style>
    /* Custom Leaflet Popup Styling to match UI */
    .leaflet-popup-content-wrapper {
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
    }
    .leaflet-popup-tip {
        background: var(--card-bg);
    }
</style>
@endsection
