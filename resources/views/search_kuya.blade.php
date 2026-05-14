@extends('layouts.app')

@section('title', 'Search A Kuya - Caragados EC')

@section('content')
<div style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.025em;">Search A <span style="color: var(--accent);">Kuya</span></h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">Find and connect with fellow Caragados Eagles Club members.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="margin-bottom: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg); padding: 1.5rem;">
        <form action="{{ route('search.kuya') }}" method="GET" id="filterForm">
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="search">Search Name</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="First, Last, or Middle Name">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="region_id">Region</label>
                    <select name="region_id" id="region_id" class="form-control" onchange="filterClubs()">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="club_id">Eagle Club Name</label>
                    <select name="club_id" id="club_id" class="form-control">
                        <option value="">All Clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" data-region-id="{{ $club->lib_region_id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="per_page">Records Per Page</label>
                    <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>ALL</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Search</button>
                    <a href="{{ route('search.kuya') }}" class="btn btn-outline" style="flex: 1; text-decoration: none; display: flex; align-items: center; justify-content: center;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Members Grid -->
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @forelse($members as $member)
            <div class="card" style="border-radius: var(--radius-lg); border: 1px solid var(--border-color); background-color: var(--card-bg); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s ease; box-shadow: var(--shadow-sm);">
                <div style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border-bottom: 1px solid var(--border-color);">
                    @if($member->profile_photo)
                        <img loading="lazy" src="{{ asset('storage/' . $member->profile_photo) }}" alt="Profile Photo" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); flex-shrink: 0;" onerror="this.onerror=null; this.outerHTML='<div style=\'width: 70px; height: 70px; border-radius: 50%; background-color: var(--bg-color); border: 2px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;\'><svg width=\'24\' height=\'24\' fill=\'none\' stroke=\'#94a3b8\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg><span style=\'font-size: 0.55rem; color: var(--text-muted); margin-top: 2px; font-weight: 600; text-transform: uppercase;\'>No Photo</span></div>'">
                    @else
                        <div style="width: 70px; height: 70px; border-radius: 50%; background-color: var(--bg-color); border: 2px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span style="font-size: 0.55rem; color: var(--text-muted); margin-top: 2px; font-weight: 600; text-transform: uppercase;">No Photo</span>
                        </div>
                    @endif
                    <div style="flex: 1; min-width: 0;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem; font-weight: 400;">Kuya</span> {{ $member->fullname }}
                        </h3>
                        <div style="font-size: 0.85rem; color: var(--accent); font-weight: 600; margin-bottom: 0.15rem;">{{ $member->club->name ?? 'No Club' }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $member->region->name ?? 'No Region' }}</div>
                        @if($member->current_job || $member->office)
                            <div style="font-size: 0.8rem; color: var(--text-main); margin-top: 0.35rem; font-weight: 500;">
                                {{ $member->current_job ?: 'No Job' }} @if($member->office) • {{ $member->office }} @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div style="padding: 1rem 1.5rem; background-color: rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                        <span style="display: block; font-weight: 600; color: var(--secondary); text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.05em; margin-bottom: 0.15rem;">Contact Number</span>
                        {{ $member->contact_number ?: 'Not Provided' }}
                    </div>
                    @if($member->location)
                        <a href="{{ route('profile.location') }}" title="View on Member Mapping" style="color: var(--accent); text-decoration: none;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background-color: var(--card-bg); border: 1px dashed var(--border-color); border-radius: var(--radius-lg); color: var(--text-muted);">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 1rem; opacity: 0.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                </svg>
                <p style="font-size: 1.1rem; font-weight: 500;">No members found matching your search.</p>
                <p style="font-size: 0.9rem;">Try adjusting your filters or search keywords.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 3rem; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
        <div class="pagination-wrapper">
            {{ $members->links() }}
        </div>
    </div>
</div>

<script>
    function filterClubs() {
        const regionSelect = document.getElementById('region_id');
        const clubSelect = document.getElementById('club_id');
        const selectedRegionId = regionSelect.value;
        
        // Filter options
        const options = clubSelect.querySelectorAll('option');
        options.forEach(option => {
            const regionId = option.getAttribute('data-region-id');
            if (!regionId) return; // Skip the "All Clubs" option
            
            if (!selectedRegionId || regionId === selectedRegionId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });

        // Reset club if selected one is hidden
        const currentOption = clubSelect.options[clubSelect.selectedIndex];
        if (currentOption && currentOption.style.display === 'none') {
            clubSelect.value = "";
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('region_id').value) {
            filterClubs();
        }
    });
</script>

<style>
    /* Pagination Styling Fixes */
    .pagination {
        display: flex;
        gap: 0.25rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .page-item .page-link {
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-main);
        text-decoration: none;
        border-radius: var(--radius-md);
        font-weight: 500;
        transition: all 0.2s;
        display: block;
    }
    .page-item.active .page-link {
        background-color: var(--accent);
        border-color: var(--accent);
        color: white;
    }
    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .page-item:not(.active):not(.disabled) .page-link:hover {
        border-color: var(--accent);
        color: var(--accent);
        background-color: rgba(var(--accent-rgb), 0.05);
    }
    
    /* Hide the default Laravel pagination info since we added our own */
    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }
    
    .pagination-wrapper nav {
        display: flex;
        justify-content: center;
    }

    /* Card hover effect */
    .card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent) !important;
    }
</style>
@endsection