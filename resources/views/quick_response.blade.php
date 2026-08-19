@extends('layouts.app')

@section('title', 'Alalayang Agila Help - Caragados EC')

@section('content')
@php
    $canAddQuickResponse = auth()->user()->hasPermission('alalayang_agila', 'add');
@endphp
<!-- Leaflet CSS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div style="margin-top: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em;">Alalayang <span style="color: var(--accent);">Agila Help</span></h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Submit a Help Request</h3>
            <p class="card-description">Please select the type of help you need and provide more details below.</p>
        </div>
        <div class="card-body">
            <form action="{{ route('quick.response') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="lib_help_id" class="form-label">What can kuya do for you?</label>
                    <select name="lib_help_id" id="lib_help_id" class="form-control" required>
                        <option value="">Select help type...</option>
                        @foreach($help_list as $help)
                            <option value="{{ $help->id }}">{{ $help->name }}</option>
                        @endforeach
                    </select>
                    @error('lib_help_id')
                        <p style="color: var(--danger); font-size: 0.8rem; mt-1;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="details" class="form-label">More details</label>
                    <textarea name="details" id="details" rows="5" class="form-control" placeholder="Please describe the situation or provide more information..." required></textarea>
                    @error('details')
                        <p style="color: var(--danger); font-size: 0.8rem; mt-1;">{{ $message }}</p>
                    @enderror
                </div>

                @if($canAddQuickResponse)
                    <div class="mt-6" style="margin-top: 1rem;">
                        <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; font-weight: 600;" disabled>
                            Submit Request
                        </button>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Current Location</label>
                    <div id="location-container" style="background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem;">
                        <div id="location-status" style="flex: 1; font-size: 0.9rem; color: var(--text-muted);">
                            Fetching your location...
                        </div>
                        <input type="hidden" name="location" id="location-input">
                        <div id="location-indicator" style="width: 10px; height: 10px; border-radius: 50%; background-color: #cbd5e1;"></div>
                    </div>
                    <div id="map" style="height: 500px; width: 100%; border-radius: var(--radius-md); margin-top: 1rem; border: 1px solid var(--border-color); display: none;"></div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">We will include your current coordinates to help other Kuyas find you faster.</p>
                </div>

                @if(!$canAddQuickResponse)
                    <div style="margin-top: 1.5rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background-color: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); color: #b45309; font-size: 0.9rem;">
                        You do not have permission to submit a help request.
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div style="margin-top: 2rem; padding: 1.5rem; background-color: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1); border-radius: var(--radius-lg); display: flex; gap: 1rem; align-items: flex-start;">
        <div style="color: var(--accent); mt-0.5;">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.25rem;">Immediate Assistance</h4>
            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Once submitted, your request will be visible to nearby Kuya who can provide immediate assistance.</p>
        </div>
    </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', function() {
         const locationStatus = document.getElementById('location-status');
         const locationInput = document.getElementById('location-input');
         const locationIndicator = document.getElementById('location-indicator');
         const submitBtn = document.getElementById('submit-btn');
         const mapContainer = document.getElementById('map');
         let map;
         let marker;
 
         if ("geolocation" in navigator) {
             navigator.geolocation.getCurrentPosition(function(position) {
                 const lat = position.coords.latitude;
                 const lng = position.coords.longitude;
                 const locationStr = `${lat}, ${lng}`;
                 
                 locationInput.value = locationStr;
                 locationStatus.textContent = `Location captured: ${locationStr}`;
                 locationStatus.style.color = 'var(--text-main)';
                 locationIndicator.style.backgroundColor = 'var(--success)';
                 if (submitBtn) {
                     submitBtn.disabled = false;
                 }

                 // Initialize and show the map
                 mapContainer.style.display = 'block';
                 map = L.map('map').setView([lat, lng], 15);
                 
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
                 osm.addTo(map);
                 L.control.layers(baseMaps).addTo(map);

                 marker = L.marker([lat, lng]).addTo(map)
                     .bindPopup('Your Current Location')
                     .openPopup();
             }, function(error) {
                let errorMsg = "Unable to retrieve your location.";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = "Location access denied. Please enable it to submit a request.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = "Location information is unavailable.";
                        break;
                    case error.TIMEOUT:
                        errorMsg = "The request to get user location timed out.";
                        break;
                }
                locationStatus.textContent = errorMsg;
                locationStatus.style.color = 'var(--danger)';
                locationIndicator.style.backgroundColor = 'var(--danger)';
                // We still allow submission if it's just a timeout or unavailable, 
                // but for permission denied we might want to keep it disabled
                if (error.code !== error.PERMISSION_DENIED && submitBtn) {
                    submitBtn.disabled = false;
                    locationInput.value = "Unknown";
                }
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            locationStatus.textContent = "Geolocation is not supported by your browser.";
            locationStatus.style.color = 'var(--danger)';
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            locationInput.value = "Not Supported";
        }
    });
</script>
@endsection
