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
                        <form action="{{ route('users.updateStatus', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline" style="width: 100%; gap: 10px; border-color: var(--danger); color: var(--danger);" onmouseover="this.style.backgroundColor='var(--danger)'; this.style.color='white'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--danger)'">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Approval
                            </button>
                        </form>
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
</script>
@endsection
