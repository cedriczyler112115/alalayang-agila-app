@extends('layouts.app')

@section('title', 'Member Mapping - Caragados EC')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        <div class="mapping-filters">
            <div class="mapping-filter-group">
                <label class="mapping-filter-label" for="filterType">Filter By</label>
                <select id="filterType" class="form-control mapping-select">
                    <option value="all">All</option>
                    <option value="region">Region</option>
                    <option value="club">Clubname</option>
                </select>
            </div>

            <div class="mapping-filter-group" id="regionFilterWrap" style="display: none;">
                <label class="mapping-filter-label" for="regionFilter">Region</label>
                <select id="regionFilter" class="form-control mapping-select">
                    <option value="">All Regions</option>
                </select>
            </div>

            <div class="mapping-filter-group" id="clubFilterWrap" style="display: none;">
                <label class="mapping-filter-label" for="clubFilter">Clubname</label>
                <select id="clubFilter" class="form-control mapping-select">
                    <option value="">All Clubnames</option>
                </select>
            </div>

            <div class="mapping-filter-group mapping-filter-search">
                <label class="mapping-filter-label" for="memberSearch">Search Name</label>
                <select id="memberSearch" class="form-control" style="width: 100%;">
                    <option value="">All Members</option>
                </select>
            </div>
        </div>
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
        const regionFilter = document.getElementById('regionFilter');
        const clubFilter = document.getElementById('clubFilter');
        const filterType = document.getElementById('filterType');
        const regionFilterWrap = document.getElementById('regionFilterWrap');
        const clubFilterWrap = document.getElementById('clubFilterWrap');
        const searchSelect = $('#memberSearch');
        const markerLayer = L.layerGroup().addTo(map);

        const normalizedMembers = members
            .filter(member => member.location && member.location.includes(','))
            .map(member => {
                const coords = member.location.split(',');
                const lat = parseFloat(coords[0]);
                const lng = parseFloat(coords[1]);
                const fullName = `${member.last_name}, ${member.first_name} ${member.middle_name || ''} ${member.extension_name || ''}`.trim().replace(/\s+/g, ' ');
                const clubName = member.club ? member.club.name : 'No Club';
                const regionName = member.region ? member.region.name : 'No Region';
                const regionId = member.region ? String(member.region.id) : '';
                const clubId = member.club ? String(member.club.id) : '';
                const photoUrl = member.profile_photo ? `${storageBase}/profile-photos/${member.profile_photo.split('/').pop()}?t=${Date.now()}` : logoUrl;

                return {
                    ...member,
                    lat,
                    lng,
                    fullName,
                    clubName,
                    regionName,
                    regionId,
                    clubId,
                    photoUrl,
                };
            });

        const uniqueRegions = [...new Map(
            normalizedMembers
                .filter(member => member.regionId)
                .map(member => [member.regionId, member.regionName])
        ).entries()].sort((a, b) => a[1].localeCompare(b[1]));

        const uniqueClubs = [...new Map(
            normalizedMembers
                .filter(member => member.clubId)
                .map(member => [member.clubId, member.clubName])
        ).entries()].sort((a, b) => a[1].localeCompare(b[1]));

        uniqueRegions.forEach(([id, name]) => {
            regionFilter.insertAdjacentHTML('beforeend', `<option value="${id}">${name}</option>`);
        });

        uniqueClubs.forEach(([id, name]) => {
            clubFilter.insertAdjacentHTML('beforeend', `<option value="${id}">${name}</option>`);
        });

        normalizedMembers
            .slice()
            .sort((a, b) => a.fullName.localeCompare(b.fullName))
            .forEach(member => {
                searchSelect.append(new Option(member.fullName, String(member.id), false, false));
            });

        searchSelect.select2({
            placeholder: 'Search member name...',
            allowClear: true,
            width: '100%'
        });

        function buildPopupContent(member) {
            return `
                <div style="display: flex; gap: 12px; min-width: 250px; padding: 5px; align-items: center;">
                    <div style="flex-shrink: 0;">
                        <img loading="lazy" src="${member.photoUrl}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); display: block;" onerror="this.src='${logoUrl}'">
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 700; color: var(--text-main); font-size: 1rem; margin-bottom: 2px; line-height: 1.2;"><span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem; font-weight: 400;">Kuya</span> ${member.fullName}</div>
                        <div style="font-size: 0.8rem; color: var(--accent); font-weight: 600; margin-bottom: 2px;">${member.clubName}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">${member.regionName}</div>
                    </div>
                </div>
            `;
        }

        function updateFilterVisibility() {
            const selectedType = filterType.value;
            regionFilterWrap.style.display = selectedType === 'region' ? 'block' : 'none';
            clubFilterWrap.style.display = selectedType === 'club' ? 'block' : 'none';

            if (selectedType !== 'region') {
                regionFilter.value = '';
            }

            if (selectedType !== 'club') {
                clubFilter.value = '';
            }
        }

        function getFilteredMembers() {
            const selectedType = filterType.value;
            const selectedRegion = regionFilter.value;
            const selectedClub = clubFilter.value;
            const selectedMemberId = searchSelect.val();

            return normalizedMembers.filter(member => {
                const matchesType = selectedType === 'all'
                    || (selectedType === 'region' && (!selectedRegion || member.regionId === selectedRegion))
                    || (selectedType === 'club' && (!selectedClub || member.clubId === selectedClub));

                const matchesSearch = !selectedMemberId || String(member.id) === String(selectedMemberId);

                return matchesType && matchesSearch;
            });
        }

        function renderMembersOnMap(shouldFocusSearch = false) {
            const filteredMembers = getFilteredMembers();
            markerLayer.clearLayers();

            filteredMembers.forEach(member => {
                const marker = L.marker([member.lat, member.lng]).bindPopup(buildPopupContent(member));
                markerLayer.addLayer(marker);

                if (shouldFocusSearch && searchSelect.val() && String(member.id) === String(searchSelect.val())) {
                    marker.openPopup();
                }
            });

            if (filteredMembers.length > 0) {
                const bounds = L.latLngBounds(filteredMembers.map(member => [member.lat, member.lng]));
                map.fitBounds(bounds.pad(0.1));
            }
        }

        filterType.addEventListener('change', function () {
            updateFilterVisibility();
            renderMembersOnMap(false);
        });

        regionFilter.addEventListener('change', function () {
            renderMembersOnMap(false);
        });

        clubFilter.addEventListener('change', function () {
            renderMembersOnMap(false);
        });

        searchSelect.on('change', function () {
            renderMembersOnMap(true);
        });

        updateFilterVisibility();
        renderMembersOnMap(false);

        // If no members have locations, fit to a default view, otherwise fit bounds
        if (normalizedMembers.length > 0) {
            const group = new L.featureGroup(normalizedMembers.map(member => L.marker([member.lat, member.lng])));

            if (group.getLayers().length > 0 && !searchSelect.val()) {
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
    });
</script>

<style>
    .mapping-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--card-bg);
    }

    .mapping-filter-group {
        min-width: 180px;
        flex: 1 1 180px;
    }

    .mapping-filter-search {
        min-width: 260px;
        flex: 2 1 320px;
    }

    .mapping-filter-label {
        display: block;
        margin-bottom: 0.45rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .mapping-select,
    .mapping-filters .select2-selection {
        min-height: 42px;
        border-radius: var(--radius-md);
    }

    .mapping-filters .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        background: var(--card-bg);
    }

    .mapping-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-main);
        line-height: 40px;
        padding-left: 0.9rem;
    }

    .mapping-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 0.55rem;
    }

    .mapping-filters .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--text-muted);
    }

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
