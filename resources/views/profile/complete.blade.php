@extends('layouts.app')

@section('title', 'Complete Profile - Caragados EC')

@section('content')
<style>
    .responsive-grid-3 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .responsive-grid-3 {
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
    }
</style>
<!-- Leaflet CSS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div style="margin-top: 2rem;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Complete Your Profile</h1>
            <p class="card-description">Please provide the following details to access your dashboard.</p>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.complete') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-control" value="{{ old('middle_name', $user->middle_name) }}">
                    </div>
                </div>

                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="extension_name">Extension Name (e.g., Jr., Sr.)</label>
                        <input type="text" id="extension_name" name="extension_name" class="form-control" value="{{ old('extension_name', $user->extension_name) }}">
                    </div>
                </div>

                <div class="responsive-grid-3">
                    <div class="form-group">
                        <label class="form-label" for="sex">Sex</label>
                        <select id="sex" name="sex" class="form-control" required>
                            <option value="">Select Sex...</option>
                            <option value="Male" {{ old('sex', $user->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $user->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="birthday">Birthday</label>
                        <input type="date" id="birthday" name="birthday" class="form-control" value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="marital_status">Marital</label>
                        <select id="marital_status" name="marital_status" class="form-control" required>
                            <option value="">Select Status...</option>
                            <option value="Single" {{ old('marital_status', $user->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status', $user->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('marital_status', $user->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('marital_status', $user->marital_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2" style="display: grid; grid-template-columns: 1fr; gap: 1rem;" id="job-office-grid">
                    <div class="form-group">
                        <label class="form-label" for="current_job">Current Job / Occupation</label>
                        <input type="text" id="current_job" name="current_job" class="form-control" value="{{ old('current_job', $user->current_job) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="office">Office / Company Name</label>
                        <input type="text" id="office" name="office" class="form-control" value="{{ old('office', $user->office) }}">
                    </div>
                </div>
                <style>
                    @media (min-width: 768px) {
                        #job-office-grid { grid-template-columns: repeat(2, 1fr) !important; }
                    }
                </style>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $user->address) }}" placeholder="City, State, Country" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Address Location on Map</label>
                    <div id="map" style="height: 500px; width: 100%; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 0.5rem;"></div>
                    <input type="hidden" name="location" id="location-input" value="{{ old('location', $user->location) }}">
                    <p style="font-size: 0.8rem; color: var(--text-muted);">Click on the map to tag your precise address location.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" class="form-control" value="{{ old('contact_number', $user->contact_number) }}" placeholder="+1234567890" required>
                </div>

                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label" for="contact_person_emergency">Emergency Contact Person</label>
                        <input type="text" id="contact_person_emergency" name="contact_person_emergency" class="form-control" value="{{ old('contact_person_emergency', $user->contact_person_emergency) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_number_emergency">Emergency Contact Number</label>
                        <input type="text" id="contact_number_emergency" name="contact_number_emergency" class="form-control" value="{{ old('contact_number_emergency', $user->contact_number_emergency) }}" required>
                    </div>
                </div>
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label" for="profile_photo">Profile Photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" class="form-control" accept="image/*" {{ !$user->profile_photo ? 'required' : '' }}>
                        <div style="margin-top: 1rem;">
                            @php
                                $photoUrl = $user->profile_photo ? asset('storage/profile-photos/' . basename($user->profile_photo)) : asset('images/default-avatar.svg');
                            @endphp
                            <img id="profile_photo_preview" loading="lazy" src="{{ $photoUrl }}" alt="Profile Photo" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid var(--accent); display: block;" onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="eagle_id_card">Eagle Identification Card</label>
                        <input type="file" id="eagle_id_card" name="eagle_id_card" class="form-control" accept="image/*" {{ !$user->eagle_id_card ? 'required' : '' }}>
                        <div style="margin-top: 1rem;">
                            @php
                                $eagleUrl = $user->eagle_id_card ? asset('storage/eagle-ids/' . basename($user->eagle_id_card)) : asset('images/logo.png');
                            @endphp
                            <img id="eagle_id_card_preview" loading="lazy" src="{{ $eagleUrl }}" alt="Eagle ID Card" style="width: 100%; max-width: 250px; height: 160px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: block;" onerror="this.src='{{ asset('images/logo.png') }}'">
                        </div>
                    </div>
                </div>
                <div class="responsive-grid-3">
                    <div class="form-group">
                        <label class="form-label" for="lib_position_id">Position</label>
                        <select id="lib_position_id" name="lib_position_id" class="form-control" required>
                            <option value="">Select Position...</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('lib_position_id', $user->lib_position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="lib_region_id">Region</label>
                        <select id="lib_region_id" name="lib_region_id" class="form-control" onchange="filterClubs()" required>
                            <option value="">Select Region...</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('lib_region_id', $user->lib_region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lib_club_name_id">Eagle Club Name</label>
                        <select id="lib_club_name_id" name="lib_club_name_id" class="form-control" required disabled>
                            <option value="">Select Club...</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" data-region-id="{{ $club->lib_region_id }}" {{ old('lib_club_name_id', $user->lib_club_name_id) == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <input type="checkbox" id="make_private" name="make_private" value="1" {{ old('make_private', $user->make_private) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: var(--accent);">
                    <label class="form-label" for="make_private" style="margin-bottom: 0; cursor: pointer; color: var(--text-main); font-weight: 600;">Keep my profile private (Hide from public search and mapping)</label>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Save Profile and Continue</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Silent Image Optimization and Auto-Resize during upload
    function processImage(input, maxDim) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];
        if (!/^image\/\w+/.test(file.type)) return;

        const reader = new FileReader();
        reader.onload = function(evt) {
            const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;
                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                let quality = undefined;
                if (file.type === 'image/jpeg' || file.type === 'image/webp') {
                    quality = 0.85;
                }

                canvas.toBlob(function(blob) {
                    if (!blob) return;
                    
                    const newFile = new File([blob], file.name, { type: file.type });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(newFile);
                    input.files = dataTransfer.files;

                    const previewId = input.id + '_preview';
                    const previewImg = document.getElementById(previewId);
                    if (previewImg) previewImg.src = URL.createObjectURL(blob);
                }, file.type, quality);
            };
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('profile_photo').addEventListener('change', function() {
        processImage(this, 500); // Max 500px for profile photo
    });

    document.getElementById('eagle_id_card').addEventListener('change', function() {
        processImage(this, 800); // Max 800px for ID card
    });
</script>

<script>
    let map;
    let marker;

    function initMap() {
        const locationInput = document.getElementById('location-input');
        let initialLat = 8.9475; // Default to Butuan City/Caraga area
        let initialLng = 125.5406;
        let zoom = 12;

        if (locationInput.value && locationInput.value.includes(',')) {
            const coords = locationInput.value.split(',');
            initialLat = parseFloat(coords[0]);
            initialLng = parseFloat(coords[1]);
            zoom = 15;
        }

        map = L.map('map').setView([initialLat, initialLng], zoom);

        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EBP, and the GIS User Community'
        });

        const baseMaps = {
            "Default": osm,
            "Satellite": satellite
        };

        //satellite.addTo(map); // Satellite as default
        osm.addTo(map); //defaul layer map
        L.control.layers(baseMaps).addTo(map);

        if (locationInput.value && locationInput.value.includes(',')) {
            marker = L.marker([initialLat, initialLng]).addTo(map);
        }

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
            
            locationInput.value = `${lat}, ${lng}`;
        });
    }

    function filterClubs() {
        const regionSelect = document.getElementById('lib_region_id');
        const clubSelect = document.getElementById('lib_club_name_id');
        const selectedRegionId = regionSelect.value;
        
        clubSelect.disabled = !selectedRegionId;
        
        // Reset club selection
        clubSelect.value = "";
        
        // Filter options
        const options = clubSelect.querySelectorAll('option');
        options.forEach(option => {
            const regionId = option.getAttribute('data-region-id');
            if (!regionId) return; // Skip the "Select Club..." option
            
            if (regionId === selectedRegionId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        if (document.getElementById('lib_region_id').value) {
            filterClubs();
            // Preserve the selected club if it exists
            const currentClubId = "{{ old('lib_club_name_id', $user->lib_club_name_id) }}";
            if (currentClubId) {
                document.getElementById('lib_club_name_id').value = currentClubId;
            }
        }
    });
</script>
@endsection
