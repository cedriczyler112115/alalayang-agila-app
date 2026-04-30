@extends('layouts.app')

@section('title', 'Organizational Structure - Caragados EC')

@section('content')
<style>
    /* Override global container exactly for this structure view to be 100% full width */
    .container {
        max-width: 100% !important;
        padding-left: 2rem !important;
        padding-right: 2rem !important;
    }

    .regions-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 2rem;
        align-items: start;
    }
    
    .club-officers-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.25rem;
        width: 100%;
    }

    @media (max-width: 1024px) {
        .club-officers-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        .regions-grid {
            grid-template-columns: 1fr;
        }
        .club-officers-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .club-officers-grid {
            grid-template-columns: 1fr;
        }
    }

    .structure-focus-target {
        scroll-margin-top: 1.5rem;
        transition: box-shadow 0.25s ease, border-color 0.25s ease, background-color 0.25s ease;
        outline: none;
    }

    .structure-focus-highlight {
        border-color: #f59e0b !important;
        background-color: rgba(245, 158, 11, 0.12) !important;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.18), var(--shadow-md) !important;
    }
</style>
<div style="padding: 2rem 0;">
    <div style="display: flex; align-items: center; margin-bottom: 2.5rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="margin-right: 1rem; padding: 0.5rem; border-radius: 50%;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; letter-spacing: -0.025em;">Organizational <span style="color: var(--accent);">Structure</span></h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">Hierarchy and Authority Flow of the Philippine Eagles (Alalayang Agila)</p>
        </div>
    </div>

    <div style="width: 100%; margin: 0 auto; padding: 0 1rem;">
        
        <!-- National Level -->
        <div style="background-color: var(--primary); color: white; padding: 1rem 1.5rem; border-radius: var(--radius-lg); text-align: center; margin-bottom: 2rem; position: relative; z-index: 2; box-shadow: var(--shadow-md);">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0;">NATIONAL EXECUTIVES</h2>
        </div>

        @if($national_officers->isNotEmpty())
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; justify-content: center; margin-bottom: 2rem; position: relative; z-index: 2;">
            @foreach($national_officers as $nat_officer)
                <div style="background: white; border: 1px solid var(--dafault); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 100%; max-width: 450px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; padding: 1.5rem;">
                    @if($nat_officer->photo)
                        <img src="{{ asset('storage/' . $nat_officer->photo) }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 0.75rem; border: 3px solid var(--default);">
                    @else
                        <div style="width: 150px; height: 150px; border-radius: 50%; background-color: var(--bg-color); margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: center; border: 2px dashed #94a3b8;">
                            <svg width="40" height="40" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    @endif
                    <span style="font-weight: 700; font-size: 1.15rem; color: var(--primary); line-height: 1.2;">{{ $nat_officer->fullname }}</span>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent); margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color); width: 100%;">{{ $nat_officer->position }}</h3>
                </div>
            @endforeach
        </div>
        @endif

        <div style="width: 2px; height: 40px; background-color: var(--accent); margin: -2rem auto 0 auto; position: relative; z-index: 1;"></div>

        <!-- ======================= -->
        <!-- LEVEL 1: REGIONS VIEW   -->
        <!-- ======================= -->
        <div id="level-1-regions" class="regions-grid structure-focus-target" tabindex="-1">
            @foreach($regions as $region)
                @php
                    $regional_officers = $regional_officers_all->get($region->id, collect())->groupBy('lib_regional_position_id');
                @endphp
                
                <div id="region-card-{{ $region->id }}" class="region-card structure-focus-target" tabindex="-1" style="border: 2px solid var(--accent); background-color: rgba(59, 130, 246, 0.05); padding: 2.5rem 1.5rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; position: relative; z-index: 2; box-shadow: var(--shadow-md);">
                    
                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background-color: var(--accent); color: white; padding: 6px 20px; border-radius: 99px; font-size: 0.9rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; display: flex; align-items: center; gap: 0.5rem; white-space: nowrap;">
                        @if($region->logo)
                            <img src="{{ asset('storage/' . $region->logo) }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: white; padding: 2px;">
                        @endif
                         {{ $region->name }}
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; align-items: center;margin-top:30px">
                        @php
                            $lead_regional_pos = $regional_positions->first();
                            $lead_officers = $lead_regional_pos ? $regional_officers->get($lead_regional_pos->id, collect()) : collect();
                            $other_regional_pos = $regional_positions->skip(1);
                        @endphp
                        
                        @if($lead_regional_pos)
                            <!-- Regional Governor(s) -->
                            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; width: 100%;">
                                @if($lead_officers->isEmpty())
                                    <div style="background: white; border: 2px solid var(--border-color); padding: 1.25rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 100%; max-width: 450px; text-align: center; display: flex; flex-direction: column; align-items: center;">
                                        <div style="width: 70px; height: 70px; border-radius: 50%; background-color: var(--border-color); margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: center; border: 2px dashed #94a3b8;">
                                            <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                        <span style="font-weight: 600; font-size: 1rem; color: var(--text-muted);">Vacant</span>
                                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent); margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color); width: 100%;">{{ $lead_regional_pos->name }}</h3>
                                    </div>
                                @else
                                    @foreach($lead_officers as $lead_regional_officer)
                                        <div style="background: white; border: 2px solid var(--border-color); padding: 1.25rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 100%; max-width: 450px; text-align: center; display: flex; flex-direction: column; align-items: center;">
                                            <img src="{{ $lead_regional_officer->profile_photo ? asset('storage/' . $lead_regional_officer->profile_photo) : asset('images/default-avatar.svg') }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 0.75rem; border: 3px solid var(--border-color);">
                                            <span style="font-weight: 700; font-size: 1.15rem; color: var(--primary); line-height: 1.2;">{{ $lead_regional_officer->fullname }}</span>
                                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem; text-align: center; line-height: 1.4;">
                                                @if($lead_regional_officer->club)
                                                    <div>📍 {{ $lead_regional_officer->club->name }}</div>
                                                @endif
                                                @if($lead_regional_officer->address)
                                                    <div>🏠 {{ $lead_regional_officer->address }}</div>
                                                @endif
                                            </div>
                                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent); margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color); width: 100%;">{{ $lead_regional_pos->name }}</h3>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                        
                        <!-- Regional Officers Grid -->
                        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; width: 100%;">
                            @foreach($other_regional_pos as $position)
                                @php
                                    $officers_list = $regional_officers->get($position->id, collect());
                                @endphp
                                @if($officers_list->isEmpty())
                                    <div style="background: white; border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-md); text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <div style="width: 150px; height: 150px; border-radius: 50%; background-color: var(--bg-color); margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                            <svg width="50" height="50" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <span style="font-weight: 600; font-size: 0.85rem; color: var(--text-muted);">Vacant</span>
                                        <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--secondary); margin-top: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem; width: 100%;">{{ $position->name }}</h3>
                                    </div>
                                @else
                                    @foreach($officers_list as $officer)
                                        <div style="background: white; border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-md); text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <img src="{{ $officer->profile_photo ? asset('storage/' . $officer->profile_photo) : asset('images/default-avatar.svg') }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 0.5rem; border: 2px solid var(--border-color);">
                                            <span style="font-weight: 700; font-size: 0.95rem; color: var(--primary); line-height: 1.2;">{{ $officer->fullname }}</span>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; text-align: center; line-height: 1.4;">
                                                @if($officer->club)
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">📍 {{ $officer->club->name }}</div>
                                                @endif
                                                @if($officer->address)
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">🏠 {{ $officer->address }}</div>
                                                @endif
                                            </div>
                                            <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--secondary); margin-top: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem; width: 100%;">{{ $position->name }}</h3>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>

                        <button onclick="showClubs({{ $region->id }})" class="btn" style="background-color: var(--success); color: white; border: none; padding: 0.75rem 2rem; border-radius: var(--radius-md); font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; box-shadow: var(--shadow-sm);">
                            View Local Eagle Clubs in {{ $region->name }}
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ======================= -->
        <!-- LEVEL 2: CLUBS VIEW     -->
        <!-- ======================= -->
        <div id="level-2-clubs" class="structure-focus-target" tabindex="-1" style="display: none;">
            <div style="margin-bottom: 2rem;">
                <button id="back-to-regions-btn" onclick="showRegionsFromClubs()" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;" data-region-id="">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Regional Directorates
                </button>
            </div>

            @foreach($regions as $region)
                @php
                    $clubs = $clubs_all->get($region->id, collect());
                @endphp
                <div class="region-clubs-container structure-focus-target" id="clubs-for-region-{{ $region->id }}" tabindex="-1" style="display: none;">
                    
                    <div style="border: 2px solid var(--success); background-color: rgba(34, 197, 94, 0.05); padding: 2.5rem 1.5rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; position: relative; z-index: 2;">
                        <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background-color: var(--success); color: white; padding: 6px 20px; border-radius: 99px; font-size: 0.9rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; display: flex; align-items: center; gap: 0.5rem; white-space: nowrap;">
                            @if($region->logo)
                                <img src="{{ asset('storage/' . $region->logo) }}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; background: white; padding: 2px;">
                            @endif
                            Clubs under {{ $region->name }}
                        </div>
                        
                        @if($clubs->isEmpty())
                            <div style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                <em>No Eagle Clubs belong to this jurisdiction yet.</em>
                            </div>
                        @else
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                                @foreach($clubs as $club)
                                    <div onclick="showOfficers({{ $club->id }}, {{ $region->id }})" style="background: white; border: 2px solid var(--border-color); padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); cursor: pointer; text-align: center; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center;" onmouseover="this.style.borderColor='var(--success)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                                        @if($club->logo)
                                            <img src="{{ asset('storage/' . $club->logo) }}" style="width: 200px; height: 200px; border-radius: 50%; object-fit: contain; margin-bottom: 0.75rem;">
                                        @endif
                                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">{{ $club->name }}</h3>
                                        <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Click to view Officers Grid</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ======================= -->
        <!-- LEVEL 3: OFFICERS VIEW  -->
        <!-- ======================= -->
        <div id="level-3-officers" class="structure-focus-target" tabindex="-1" style="display: none;">
            <div style="margin-bottom: 2rem;">
                <button id="back-to-clubs-btn" onclick="showClubsFromOfficers()" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;" data-region-id="">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Local Clubs
                </button>
            </div>

            @foreach($clubs_all->flatten() as $club)
                @php
                    $club_officers = $all_club_officers->get($club->id, collect())->groupBy('lib_position_id');
                @endphp
                <div class="club-officers-container structure-focus-target" id="officers-for-club-{{ $club->id }}" tabindex="-1" style="display: none;">
                    
                    <div style="border: 2px solid var(--success); background-color: rgba(34, 197, 94, 0.05); padding: 2.5rem 1rem 1.5rem; border-radius: var(--radius-lg); position: relative; z-index: 2; box-shadow: var(--shadow-md); width: 100%;">
                        <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background-color: var(--success); color: white; padding: 6px 20px; border-radius: 99px; font-size: 0.9rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                            {{ $club->name }} Officers
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem; align-items: center;">
                            @php
                                $lead_club_pos = $club_positions->first();
                                $lead_club_officers = $lead_club_pos ? $club_officers->get($lead_club_pos->id, collect()) : collect();
                                $other_club_pos = $club_positions->skip(1);
                            @endphp
                            
                            @if($lead_club_pos)
                                <!-- Club President(s) -->
                                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; width: 100%;">
                                    @if($lead_club_officers->isEmpty())
                                        <div style="background: white; border: 2px solid var(--border-color); padding: 1.25rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; max-width: 450px;">
                                            <div style="width: 70px; height: 70px; border-radius: 50%; background-color: var(--border-color); margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: center; border: 2px dashed #94a3b8;">
                                                <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                            <span style="font-weight: 600; font-size: 1rem; color: var(--text-muted);">Vacant</span>
                                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--success); margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color); width: 100%;">{{ $lead_club_pos->name }}</h3>
                                        </div>
                                    @else
                                        @foreach($lead_club_officers as $lead_club_officer)
                                            <div style="background: white; border: 2px solid var(--border-color); padding: 1.25rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; max-width: 450px;">
                                                <img src="{{ $lead_club_officer->profile_photo ? asset('storage/' . $lead_club_officer->profile_photo) : asset('images/default-avatar.svg') }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 0.75rem; border: 3px solid var(--border-color);">
                                                <span style="font-weight: 700; font-size: 1.15rem; color: var(--primary); line-height: 1.2;">{{ $lead_club_officer->fullname }}</span>
                                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem; text-align: center; line-height: 1.4;">
                                                    @if($lead_club_officer->club)
                                                        <div>📍 {{ $lead_club_officer->club->name }}</div>
                                                    @endif
                                                    @if($lead_club_officer->address)
                                                        <div>🏠 {{ $lead_club_officer->address }}</div>
                                                    @endif
                                                </div>
                                                <h3 style="font-size: 1rem; font-weight: 700; color: var(--success); margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color); width: 100%;">{{ $lead_club_pos->name }}</h3>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Club Officers Grid (Responsive 5-columns natively!) -->
                            <div class="club-officers-grid">
                                @foreach($other_club_pos as $position)
                                    @php
                                        $officers_list = $club_officers->get($position->id, collect());
                                    @endphp
                                    @if($officers_list->isEmpty())
                                        <div style="background: white; border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-md); text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <div style="width: 150px; height: 150px; border-radius: 50%; background-color: var(--bg-color); margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                                <svg width="50" height="50" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <span style="font-weight: 600; font-size: 0.85rem; color: var(--text-muted);">Vacant</span>
                                            <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--secondary); margin-top: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem; width: 100%;">{{ $position->name }}</h3>
                                        </div>
                                    @else
                                        @foreach($officers_list as $officer)
                                            <div style="background: white; border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-md); text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                <img src="{{ $officer->profile_photo ? asset('storage/' . $officer->profile_photo) : asset('images/default-avatar.svg') }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 0.5rem; border: 2px solid var(--border-color);">
                                                <span style="font-weight: 700; font-size: 0.95rem; color: var(--primary); line-height: 1.2;">{{ $officer->fullname }}</span>
                                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; text-align: center; line-height: 1.4;">
                                                    @if($officer->club)
                                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">📍 {{ $officer->club->name }}</div>
                                                    @endif
                                                    @if($officer->address)
                                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">🏠 {{ $officer->address }}</div>
                                                    @endif
                                                </div>
                                                <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--secondary); margin-top: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem; width: 100%;">{{ $position->name }}</h3>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</div>

<script>
    let activeRegionId = null;
    let activeClubId = null;

    function focusStructureTarget(targetId, fallbackId = null) {
        const target = document.getElementById(targetId) || (fallbackId ? document.getElementById(fallbackId) : null);

        if (!target) {
            console.warn('Structure focus target not found.', { targetId, fallbackId });
            return false;
        }

        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
                const rect = target.getBoundingClientRect();
                const destination = Math.max(scrollY + rect.top - 24, 0);

                try {
                    target.focus({ preventScroll: true });
                } catch (error) {
                    target.focus();
                }

                if ('scrollBehavior' in document.documentElement.style) {
                    window.scrollTo({ top: destination, behavior: 'smooth' });
                } else {
                    window.scrollTo(0, destination);
                }

                target.classList.remove('structure-focus-highlight');
                void target.offsetWidth;
                target.classList.add('structure-focus-highlight');

                window.setTimeout(() => {
                    target.classList.remove('structure-focus-highlight');
                }, 1800);
            });
        });

        return true;
    }

    function showRegions(regionId = null) {
        document.getElementById('level-1-regions').style.display = 'grid';
        document.getElementById('level-2-clubs').style.display = 'none';
        document.getElementById('level-3-officers').style.display = 'none';

        if (regionId) {
            activeRegionId = String(regionId);
        }

        focusStructureTarget(
            activeRegionId ? 'region-card-' + activeRegionId : 'level-1-regions',
            'level-1-regions'
        );
    }

    function showClubs(regionId) {
        activeRegionId = String(regionId);
        activeClubId = null;

        document.getElementById('level-1-regions').style.display = 'none';
        document.getElementById('level-3-officers').style.display = 'none';
        document.getElementById('level-2-clubs').style.display = 'block';
        
        // Hide all region clubs
        document.querySelectorAll('.region-clubs-container').forEach(el => el.style.display = 'none');
        
        // Show specific region clubs
        let target = document.getElementById('clubs-for-region-' + regionId);
        if(target) target.style.display = 'block';

        let backToRegionsBtn = document.getElementById('back-to-regions-btn');
        if (backToRegionsBtn) {
            backToRegionsBtn.setAttribute('data-region-id', regionId);
        }

        focusStructureTarget('clubs-for-region-' + regionId, 'level-2-clubs');
    }

    function showOfficers(clubId, regionId) {
        activeRegionId = String(regionId);
        activeClubId = String(clubId);

        document.getElementById('level-1-regions').style.display = 'none';
        document.getElementById('level-2-clubs').style.display = 'none';
        document.getElementById('level-3-officers').style.display = 'block';
        
        // Hide all club officers
        document.querySelectorAll('.club-officers-container').forEach(el => el.style.display = 'none');
        
        // Show specific club officers
        let target = document.getElementById('officers-for-club-' + clubId);
        if(target) target.style.display = 'block';
        
        // Store the regionId for the back button
        let backBtn = document.getElementById('back-to-clubs-btn');
        if(backBtn) {
            backBtn.setAttribute('data-region-id', regionId);
        }

        focusStructureTarget('officers-for-club-' + clubId, 'level-3-officers');
    }

    function showRegionsFromClubs() {
        let btn = document.getElementById('back-to-regions-btn');
        let regionId = btn ? btn.getAttribute('data-region-id') : activeRegionId;
        showRegions(regionId);
    }

    function showClubsFromOfficers() {
        let btn = document.getElementById('back-to-clubs-btn');
        let regionId = btn ? btn.getAttribute('data-region-id') : activeRegionId;
        if(regionId) {
            showClubs(regionId);
        } else {
            showRegions();
        }
    }
</script>
@endsection
