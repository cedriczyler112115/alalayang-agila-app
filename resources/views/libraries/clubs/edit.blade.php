@extends('layouts.app')

@section('title', 'Edit Club Officers - Caragados EC')

@section('content')
<div style="margin-top: 2rem;">
    <!-- Include jQuery and Select2 Dependencies Local/CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Automatically adjust Select2 container height for custom inner HTML */
        .select2-container .select2-selection--single {
            height: auto !important;
            min-height: 44px;
            padding: 4px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0.5rem;
            width: 100%;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
        }
    </style>
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('libraries.index', ['tab' => 'clubs']) }}" class="btn btn-outline" style="margin-right: 1rem; padding: 0.5rem; border-radius: 50%;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; letter-spacing: -0.025em;">Edit <span style="color: var(--accent);">Eagle Club</span></h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">Manage the club details and dynamically assign officers to positions.</p>
        </div>
    </div>

    <!-- Toast Alert for AJAX save success -->
    <div id="ajax-status-toast" style="display: none; background-color: rgba(34, 197, 94, 0.1); color: var(--success); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem;">
        Assignment saved!
    </div>

    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        @if($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem;">
                <ul style="margin-left: 1.5rem; margin-bottom: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('libraries.club.update', $club->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">Club Identification</h2>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label">Mapped Region <span style="color: var(--danger);">*</span></label>
                <select name="lib_region_id" class="form-control" required>
                    <option value="">Select Region...</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ $region->id == $club->lib_region_id ? 'selected' : '' }}>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" for="name">Club Name <span style="color: var(--danger);">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $club->name) }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" for="color">Map Pin Color</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="color" id="color_picker" value="{{ old('color', $club->color ?? '#3B82F6') }}" style="width: 50px; height: 42px; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2px; cursor: pointer;" onchange="document.getElementById('color').value = this.value">
                    <input type="text" id="color" name="color" class="form-control" value="{{ old('color', $club->color ?? '#3B82F6') }}" maxlength="30" placeholder="#3B82F6" onchange="document.getElementById('color_picker').value = this.value">
                </div>
                <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">Color of the pin marker on the member location map.</small>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" for="notification_keyword">Notification keyword</label>
                @if(auth()->user()->is_admin)
                    <input type="text" id="notification_keyword" name="notification_keyword" class="form-control" value="{{ old('notification_keyword', $club->notification_keyword) }}" maxlength="255" placeholder="Enter notification keyword">
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">Admin only. Maximum 255 characters.</small>
                @else
                    <input type="text" id="notification_keyword" class="form-control" value="{{ $club->notification_keyword }}" disabled>
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">Only administrators can update this field.</small>
                @endif
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" for="logo">Club Logo</label>
                <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                @if($club->logo)
                    <div style="margin-top: 1rem;">
                        <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }} Logo" style="max-width: 150px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    </div>
                @endif
            </div>

            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">Assign Dynamic Officers</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Select active users to fill the club's officer positions. Users who are already actively serving in another club will not appear in the available dropdown lists.</p>

            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                @foreach($positions as $position)
                    @php
                        // Check if the club has an active officer assigned to this specific position
                        $assignedOfficer = $current_officers->firstWhere('lib_position_id', $position->id);
                    @endphp
                    <div class="form-group" style="background-color: rgba(0,0,0,0.02); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <label class="form-label" style="font-weight: 700; color: var(--secondary);">{{ $position->name }}</label>
                        <select name="officers[{{ $position->id }}]" class="form-control officer-select" data-position-id="{{ $position->id }}">
                            <option value=""></option>
                            
                            @if($assignedOfficer)
                                <option value="{{ $assignedOfficer->id }}" 
                                        data-avatar="{{ $assignedOfficer->profile_photo ? asset('storage/' . $assignedOfficer->profile_photo) : asset('images/default-avatar.svg') }}"
                                        data-club="{{ $assignedOfficer->club ? $assignedOfficer->club->name : 'N/A' }}" selected>
                                    {{ $assignedOfficer->last_name }}, {{ $assignedOfficer->first_name }} {{ $assignedOfficer->middle_name }}
                                </option>
                            @endif

                            @foreach($available_users as $user)
                                <option value="{{ $user->id }}"
                                        data-avatar="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.svg') }}"
                                        data-club="{{ $user->club ? $user->club->name : 'N/A' }}">
                                    {{ $user->last_name }}, {{ $user->first_name }} {{ $user->middle_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.8rem;">Save Club Name & Region</button>
                <a href="{{ route('libraries.index', ['tab' => 'clubs']) }}" class="btn btn-outline" style="flex: 1; padding: 0.8rem;">Finish & Go Back</a>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    function formatUserOption(state) {
        if (!state.id) {
            return state.text;
        }
        let avatar = $(state.element).data('avatar');
        let club = $(state.element).data('club');
        
        // Escape HTML values from attributes securely
        let safeText = $('<div>').text(state.text).html();
        let safeClub = $('<div>').text(club).html();
        
        let $html = $(
            '<div style="display: flex; align-items: center; gap: 12px; padding: 4px 0;">' +
                '<img src="' + avatar + '" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" onerror="this.src=\'{{ asset("images/default-avatar.svg") }}\'" />' +
                '<div style="display: flex; flex-direction: column;">' +
                    '<span style="font-weight: 600; font-size: 0.95rem; line-height: 1.2;">' + safeText + '</span>' +
                    '<span style="font-size: 0.75rem; color: var(--text-muted);">' + safeClub + '</span>' +
                '</div>' +
            '</div>'
        );
        return $html;
    }

    $('.officer-select').select2({
        width: '100%',
        placeholder: "-- No Officer Assigned --",
        allowClear: true,
        templateResult: formatUserOption,
        templateSelection: formatUserOption,
        escapeMarkup: function(m) { return m; } // let our custom formatter render HTML
    });

    // Store previous value to release when changed dynamically
    $('.officer-select').on('select2:opening', function() {
        $(this).data('previous', $(this).val());
    });

    $('.officer-select').on('change', function(e) {
        let currentUserId = $(this).val();
        let prevUserId = $(this).data('previous');
        let positionId = $(this).data('position-id');
        let elToggle = this;

        // Disable selected user in all other dropdowns explicitly
        if (currentUserId) {
            $('.officer-select').not(this).find('option[value="' + currentUserId + '"]').prop('disabled', true);
        }
        
        // Re-enable previously selected user cleanly
        if (prevUserId && prevUserId !== currentUserId) {
            $('.officer-select').not(this).find('option[value="' + prevUserId + '"]').prop('disabled', false);
        }
        
        // Refresh Select2 visual bindings globally
        $('.officer-select').select2({ width: '100%', allowClear: true, placeholder: "-- No Officer Assigned --", templateResult: formatUserOption, templateSelection: formatUserOption, escapeMarkup: function(m) { return m; } });

        // Auto-save over asynchronous API
        fetch(`{{ route('libraries.club.assign_officer', $club->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                position_id: positionId,
                user_id: currentUserId || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Toast logic
                let toast = document.getElementById('ajax-status-toast');
                toast.style.display = 'block';
                setTimeout(() => toast.style.display = 'none', 2500);
            } else {
                alert('An error occurred during save.');
            }
        });

        // Mutate local track
        $(this).data('previous', currentUserId);
    });

    // Lock options globally for items that are pre-selected on page load
    $('.officer-select').each(function() {
        let val = $(this).val();
        if(val) {
            $('.officer-select').not(this).find('option[value="' + val + '"]').prop('disabled', true);
        }
    });

});
</script>
@endsection
