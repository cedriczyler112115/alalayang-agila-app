@extends('layouts.app')

@section('title', 'Users Management - Caragados EC')

@section('content')
<div style="margin-top: 2rem; margin-bottom: 4rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.025em;">
                Users <span style="color: var(--accent);">Management</span>
            </h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Manage club members and verify new registrations.</p>
        </div>
        <div style="background-color: var(--card-bg); padding: 0.75rem 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px; box-shadow: var(--shadow-sm);">
            <div style="width: 12px; height: 12px; background-color: var(--accent); border-radius: 50%;"></div>
            <span style="font-weight: 700; font-size: 1rem; color: var(--text-main);">{{ $users->total() }} Total Users</span>
        </div>
    </div>

    @if(session('success'))
        <div style="background-color: #dcfce7; color: #166534; padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters Section -->
    <div class="card" style="margin-bottom: 3rem; border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
        <div class="card-body" style="padding: 2rem;">
            <form action="{{ route('users.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Search User</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or Email..." value="{{ request('search') }}">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Region</label>
                    <select name="region_id" id="region-filter" class="form-control">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Club</label>
                    <select name="club_id" id="club-filter" class="form-control">
                        <option value="">All Clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Pending Proof of Payment</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; height: 45px; background-color: var(--accent);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline" style="height: 45px;">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        @forelse($users as $user)

            <div class="card" style="border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'">
                <div style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border-bottom: 1px solid var(--border-color); position: relative;">
                    <div style="position: absolute; top: 1.5rem; right: 1.5rem;">
                        <span style="padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; background-color: {{ $user->status == 1 ? '#dcfce7' : '#fef9c3' }}; color: {{ $user->status == 1 ? '#166534' : '#854d0e' }}; border: 1px solid {{ $user->status == 1 ? '#bbf7d0' : '#fef08a' }};">
                            {{ $user->status == 1 ? 'Active' : 'Pending' }}
                        </span>
                    </div>  
                        
                    @if($user->profile_photo)
                        <img loading="lazy" src="{{ asset('storage/profile-photos/' . basename($user->profile_photo)) }}" alt="Profile Photo" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); flex-shrink: 0;" onerror="this.onerror=null; this.outerHTML='<div style=\'width: 70px; height: 70px; border-radius: 50%; background-color: var(--bg-color); border: 2px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;\'><svg width=\'24\' height=\'24\' fill=\'none\' stroke=\'#94a3b8\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg><span style=\'font-size: 0.55rem; color: var(--text-muted); margin-top: 2px; font-weight: 600; text-transform: uppercase;\'>No Photo</span></div>'">
                    @else
                        <div style="width: 70px; height: 70px; border-radius: 50%; background-color: var(--bg-color); border: 2px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span style="font-size: 0.55rem; color: var(--text-muted); margin-top: 2px; font-weight: 600; text-transform: uppercase;">No Photo</span>
                        </div>
                    @endif
                    
                    <div style="flex: 1; min-width: 0; padding-right: 5rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.15rem;">
                            {{ $user->fullname }}
                        </h3>
                        @php
                            $pendingPayment = $user->subscriptionPayments->first();
                        @endphp
                        @if($pendingPayment)
                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; background-color: rgba(245, 158, 11, 0.1); color: #d97706; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; margin-top: 4px;">
                            Payment Proof Pending
                        </span>
                        @endif
                    </div>
                </div>

                <div class="card-body" style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-size: 0.9rem;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $user->region->name ?? 'N/A' }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-size: 0.9rem;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $user->club->name ?? 'N/A' }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-size: 0.9rem;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $user->email }}
                        </div>
                        @if($user->date_approve)
                            <div style="display: flex; align-items: center; gap: 10px; color: var(--success); font-size: 0.85rem; font-weight: 500; margin-top: 0.25rem;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Approved: {{ $user->date_approve->format('M d, Y h:i A') }}
                            </div>
                        @endif
                    </div>

                    @if($user->status == 0)
                        <form action="{{ route('users.updateStatus', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="width: 100%; gap: 10px; background-color: var(--success); border-color: var(--success);">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Approve & Activate
                            </button>
                        </form>
                    @else
                        <div style="display: flex; gap: 0.5rem; width: 100%;">
                            <form action="{{ route('users.updateStatus', $user) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="width: 100%; gap: 10px; border-color: var(--danger); color: var(--danger);" onmouseover="this.style.backgroundColor='var(--danger)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--danger)'">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel
                                </button>
                            </form>
                            @if(auth()->user()->is_admin)
                            <button type="button" class="btn btn-primary" style="flex: 1; gap: 10px; background-color: var(--accent); border-color: var(--accent);" onclick="openPermissionsModal({{ $user->id }}, '{{ addslashes($user->fullname) }}', {{ $user->is_admin ? 'true' : 'false' }}, '{{ $user->access_type_id }}')">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Permissions
                            </button>
                            @endif
                        </div>
                        
                        @if($pendingPayment && auth()->user()->is_admin)
                        <button type="button" class="btn btn-outline" style="width: 100%; margin-top: 0.5rem; border-color: #f59e0b; color: #d97706;" onclick="openPaymentProofModal({{ $pendingPayment->id }}, '{{ asset('storage/' . $pendingPayment->receipt_path) }}', '{{ addslashes($user->fullname) }}', '{{ addslashes($user->position->name ?? 'No Position Assigned') }}', '{{ $user->access_type_id }}')">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Payment Proof
                        </button>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 4rem; text-align: center; background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
                <div style="color: var(--text-muted); margin-bottom: 1rem;">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a5.946 5.946 0 00-.83 1.958L5.03 20.206m6.23-7.706L12 12.75m0 0l.738.75m0 0l.232.232m0 0a3 3 0 104.243-4.243m-8.485 0a3 3 0 104.243 4.243M10.5 8.25c0 .131.013.259.038.384m.562-7.134a9.147 9.147 0 01.353 3.53m0 0A4.444 4.444 0 008.25 12c.13 0 .258-.013.384-.038"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem;">No users found</h3>
                <p style="color: var(--text-muted);">Try adjusting your search or filters to find what you're looking for.</p>
            </div>
        @endforelse
    </div>

    <!-- Permissions Modal -->
    <div id="permissionsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
        <div style="background: var(--card-bg); width: 100%; max-width: 600px; border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.25rem; font-weight: 600;">Manage Permissions: <span id="permUserName" style="color: var(--accent);"></span></h3>
                <button type="button" onclick="closePermissionsModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="permissionsForm" method="POST" style="overflow-y: auto; padding: 1.5rem;">
                @csrf
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="is_admin" id="isAdminCheckbox" style="width: 18px; height: 18px;">
                        Make this user an Administrator
                    </label>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Administrators have full access to all modules and can manage other users' permissions.</p>
                </div>
                
                <div id="accessTypeSection">
                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Access Type</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">Select the role for this user to automatically apply predefined permissions.</p>
                    <select name="access_type_id" id="accessTypeSelect" class="form-control" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-md);">
                        <option value="">No Access Type</option>
                        @foreach($accessTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closePermissionsModal()" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--accent);">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 3rem; display: flex; justify-content: center;">
        {{ $users->links('pagination::bootstrap-4') }}
    </div>
</div>

<style>
    /* Custom styles for the form controls in the dark/professional theme */
    .form-control {
        background-color: var(--bg-color);
        border: 1px solid var(--border-color);
        color: var(--text-main);
    }
    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
    }
    .page-item .page-link {
        border-radius: var(--radius-md);
        padding: 0.5rem 1rem;
        color: var(--text-main);
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .page-item.active .page-link {
        background-color: var(--accent);
        border-color: var(--accent);
        color: white;
    }
    .page-item:not(.active) .page-link:hover {
        background-color: var(--bg-color);
        border-color: var(--accent);
        color: var(--accent);
    }
    .page-item.disabled .page-link {
        color: var(--text-muted);
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... Existing Region/Club filter logic ...
        const regionFilter = document.getElementById('region-filter');
        const clubFilter = document.getElementById('club-filter');

        regionFilter.addEventListener('change', function() {
            const regionId = this.value;
            
            // Reset club options
            clubFilter.innerHTML = '<option value="">All Clubs</option>';

            if (regionId) {
                const url = `{{ url('/libraries/clubs-by-region') }}/${regionId}`;
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(clubs => {
                        clubFilter.innerHTML = '<option value="">All Clubs</option>';
                        clubs.forEach(club => {
                            const option = document.createElement('option');
                            option.value = club.id;
                            option.textContent = club.name;
                            clubFilter.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching clubs:', error);
                        clubFilter.innerHTML = '<option value="">Error loading clubs</option>';
                    });
            } else {
                // If "All Regions" is selected, fetch all clubs
                const url = `{{ url('/libraries/clubs-by-region/all') }}`;
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(clubs => {
                        clubFilter.innerHTML = '<option value="">All Clubs</option>';
                        clubs.forEach(club => {
                            const option = document.createElement('option');
                            option.value = club.id;
                            option.textContent = club.name;
                            clubFilter.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching clubs:', error);
                        clubFilter.innerHTML = '<option value="">Error loading clubs</option>';
                    });
            }
        });
    });

    function openPermissionsModal(userId, userName, isAdmin, accessTypeId) {
        document.getElementById('permissionsModal').style.display = 'flex';
        document.getElementById('permUserName').textContent = userName;
        
        const form = document.getElementById('permissionsForm');
        form.action = "{{ url('/users') }}/" + userId + "/permissions";
        
        const isAdminCheckbox = document.getElementById('isAdminCheckbox');
        isAdminCheckbox.checked = isAdmin;
        
        const accessTypeSelect = document.getElementById('accessTypeSelect');
        accessTypeSelect.value = accessTypeId || '';
    }

    function closePermissionsModal() {
        document.getElementById('permissionsModal').style.display = 'none';
    }

    function openPaymentProofModal(paymentId, imageUrl, userName, positionName, accessTypeId) {
        document.getElementById('paymentProofModal').style.display = 'flex';
        document.getElementById('paymentUserName').textContent = userName;
        document.getElementById('paymentImage').src = imageUrl;
        
        document.getElementById('paymentUserPosition').textContent = positionName;
        document.getElementById('paymentAccessTypeSelect').value = accessTypeId || '';
        
        document.getElementById('paymentActionForm').action = "{{ url('/subscription-payment') }}/" + paymentId + "/update";
    }

    function closePaymentProofModal() {
        document.getElementById('paymentProofModal').style.display = 'none';
    }
</script>

<!-- Payment Proof Modal -->
<div id="paymentProofModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 50; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 600px; background-color: var(--card-bg);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" style="margin: 0;">Payment Proof: <span id="paymentUserName" style="color: var(--accent);"></span></h2>
            <button type="button" onclick="closePaymentProofModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="card-body">
            <div style="text-align: center; margin-bottom: 1.5rem; background-color: var(--bg-color); padding: 1rem; border-radius: var(--radius-md);">
                <img id="paymentImage" src="" alt="Payment Proof" style="max-width: 100%; max-height: 400px; object-fit: contain; border-radius: var(--radius-sm);">
            </div>
            
            <form id="paymentActionForm" method="POST">
                @csrf
                <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; text-align: left; padding: 1.25rem; background-color: var(--bg-color); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                    <div style="flex: 1;">
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Position:</span>
                        <div id="paymentUserPosition" style="font-size: 1rem; color: var(--text-main); font-weight: 500; margin-top: 0.25rem;"></div>
                    </div>
                    
                    <div style="flex: 1;">
                        <label for="paymentAccessTypeSelect" style="display: block; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Access Type:</label>
                        <select name="access_type_id" id="paymentAccessTypeSelect" class="form-control" style="width: 100%;">
                            <option value="">-- Select Access Type --</option>
                            @foreach($accessTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="status" value="approved" class="btn btn-primary" style="flex: 1; background-color: var(--success); border-color: var(--success);">
                        Approve Payment
                    </button>
                    <button type="submit" name="status" value="rejected" class="btn btn-outline" style="flex: 1; border-color: var(--danger); color: var(--danger);">
                        Reject Payment
                    </button>
                </div>
            </form>
            <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 1rem;">
                * Note: Approving the payment will also update the user's Access Type to the selection above.
            </p>
        </div>
    </div>
</div>
@endsection
