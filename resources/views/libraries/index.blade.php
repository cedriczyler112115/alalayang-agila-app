@extends('layouts.app')

@section('title', 'Libraries - Caragados EC')

@section('content')
@php
    $canAddLibraries = auth()->user()->hasPermission('libraries', 'add');
    $canEditLibraries = auth()->user()->hasPermission('libraries', 'edit');
    $canDeleteLibraries = auth()->user()->hasPermission('libraries', 'delete');
@endphp
<style>
    .library-tabs-container {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.75rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
    }

    .library-tabs-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }

    .library-tab-pill {
        border-radius: 9999px;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--text-muted);
        background-color: transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .library-tab-pill:hover:not(.active) {
        color: var(--accent) !important;
        background-color: rgba(59, 130, 246, 0.15) !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
    }

    .library-tab-pill.active {
        color: #ffffff !important;
        background-color: var(--accent) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
    }

    .library-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1.25rem 1.5rem;
    }

    .table-responsive {
        width: 100%;
        overflow: auto;
        max-height: 65vh;
        -webkit-overflow-scrolling: touch;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        position: relative;
    }

    .library-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 580px;
    }

    .library-table thead th {
        position: sticky;
        top: 0;
        background-color: var(--card-bg);
        z-index: 10;
        box-shadow: inset 0 -1px 0 var(--border-color);
        padding: 1rem;
        font-weight: 700;
        text-align: left;
    }

    .library-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .action-btn-group {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        align-items: center;
        white-space: nowrap;
    }

    .library-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }

    .library-modal-dialog {
        width: 100%;
        max-width: 440px;
        max-height: 90vh;
        overflow-y: auto;
        margin: auto;
        box-shadow: var(--shadow-lg);
    }

    @media (max-width: 640px) {
        .table-responsive {
            max-height: 60vh;
        }

        .library-card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .library-card-header .btn {
            width: 100%;
            justify-content: center;
        }

        .btn-mobile-full {
            width: 100%;
        }

        .library-table thead th, 
        .library-table tbody td {
            padding: 0.75rem 0.65rem !important;
            font-size: 0.85rem;
        }
    }
</style>

<div style="margin-top: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding: 0.5rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;" title="Back to Dashboard">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; margin: 0;">Dynamic <span style="color: var(--accent);">Libraries</span></h1>
    </div>

    @if(session('status'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="library-tabs-container">
        <a href="{{ route('libraries.index', ['tab' => 'global']) }}" class="library-tab-pill {{ $tab === 'global' ? 'active' : '' }}">Global</a>
        <a href="{{ route('libraries.index', ['tab' => 'regions']) }}" class="library-tab-pill {{ $tab === 'regions' ? 'active' : '' }}">Regions</a>
        <a href="{{ route('libraries.index', ['tab' => 'regional-positions']) }}" class="library-tab-pill {{ $tab === 'regional-positions' ? 'active' : '' }}">Regional Positions</a>
        <a href="{{ route('libraries.index', ['tab' => 'clubs']) }}" class="library-tab-pill {{ $tab === 'clubs' ? 'active' : '' }}">Eagle Club Names</a>
        <a href="{{ route('libraries.index', ['tab' => 'help']) }}" class="library-tab-pill {{ $tab === 'help' ? 'active' : '' }}">Help Types</a>
        <a href="{{ route('libraries.index', ['tab' => 'positions']) }}" class="library-tab-pill {{ $tab === 'positions' ? 'active' : '' }}">Club Positions</a>
        <a href="{{ route('libraries.index', ['tab' => 'national-officers']) }}" class="library-tab-pill {{ $tab === 'national-officers' ? 'active' : '' }}">National Officers</a>
    </div>

    @if($tab === 'global')
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">Global Keywords</h3>
                @if(auth()->user()->is_admin)
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Administrator only</span>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <form action="{{ route('libraries.global_keyword.update') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="library-table">
                            <thead>
                                <tr>
                                    <th>DESC</th>
                                    <th>Keyword Value</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($global_keywords as $globalKeyword)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $globalKeyword->desc }}</td>
                                        <td>
                                            @if(auth()->user()->is_admin)
                                                <input type="text" name="keywords[{{ $globalKeyword->desc }}]" class="form-control" value="{{ old('keywords.' . $globalKeyword->desc, $globalKeyword->keyword) }}" maxlength="255">
                                            @else
                                                <input type="text" class="form-control" value="{{ $globalKeyword->keyword }}" disabled>
                                            @endif
                                        </td>
                                        <td>{{ $globalKeyword->creator?->fullname ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(auth()->user()->is_admin)
                        <div style="display: flex; justify-content: flex-end; padding: 1.25rem;">
                            <button type="submit" class="btn btn-primary btn-mobile-full" style="padding: 0.8rem 1.2rem;">Save Global Keywords</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @elseif($tab === 'regions')
        <!-- Regions CRUD -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">Regions Management</h3>
                @if($canAddLibraries)
                    <button onclick="document.getElementById('addRegionModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Region</button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="library-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Notification keyword (Ntfy PUSH notification)</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regions as $region)
                            <tr>
                                <td>{{ $region->id }}</td>
                                <td style="font-weight: 600;">{{ $region->name }}</td>
                                <td>{{ $region->notification_keyword ?? 'N/A' }}</td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if($canEditLibraries)
                                            <a href="{{ route('libraries.region.edit', $region->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                        @endif
                                        @if($canDeleteLibraries)
                                            <form action="{{ route('libraries.region.destroy', $region) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Region Modal -->
        @if($canAddLibraries)
            <div id="addRegionModal" class="library-modal">
                <div class="card library-modal-dialog">
                    <div class="card-header"><h3 class="card-title">Add Region</h3></div>
                    <div class="card-body">
                        <form action="{{ route('libraries.region.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Region Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                                <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                                <button type="button" onclick="document.getElementById('addRegionModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    @elseif($tab === 'clubs')
        <!-- Clubs CRUD -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">Eagle Clubs Management</h3>
                @if($canAddLibraries)
                    <button onclick="document.getElementById('addClubModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Club</button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="library-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Region</th>
                                <th>Club Name</th>
                                <th>Notification keyword (Ntfy PUSH notification)</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clubs as $club)
                            <tr>
                                <td>{{ $club->id }}</td>
                                <td>{{ $club->region->name ?? 'N/A' }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background-color: {{ $club->color ?? '#3B82F6' }}; border: 1px solid rgba(0,0,0,0.15); flex-shrink: 0;" title="{{ $club->color }}"></span>
                                        <span style="font-weight: 600;">{{ $club->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $club->notification_keyword ?? 'N/A' }}</td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if($canEditLibraries)
                                            <a href="{{ route('libraries.club.edit', $club->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                        @endif
                                        @if($canDeleteLibraries)
                                            <form action="{{ route('libraries.club.destroy', $club) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Club Modal -->
        @if($canAddLibraries)
        <div id="addClubModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Add Club</h3></div>
                <div class="card-body">
                    <form action="{{ route('libraries.club.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Select Region</label>
                            <select name="lib_region_id" class="form-control" required>
                                <option value="">Select Region...</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Club Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-top: 1rem;">
                            <label class="form-label">Map Pin Color</label>
                            <input type="color" name="color" class="form-control" value="#3B82F6" style="height: 40px; padding: 2px; cursor: pointer;">
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                            <button type="button" onclick="document.getElementById('addClubModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    @elseif($tab === 'help')
        <!-- Help Types CRUD -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">Help Types Management</h3>
                @if($canAddLibraries)
                    <button onclick="document.getElementById('addHelpModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Help Type</button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="library-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($help_types as $help)
                            <tr>
                                <td>{{ $help->id }}</td>
                                <td style="font-weight: 600;">{{ $help->name }}</td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if($canEditLibraries)
                                            <button 
                                                onclick="editHelp(this)" 
                                                data-id="{{ $help->id }}" 
                                                data-name="{{ $help->name }}"
                                                class="btn btn-outline btn-sm">Edit</button>
                                        @endif
                                        @if($canDeleteLibraries)
                                            <form action="{{ route('libraries.help.destroy', $help) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Help Modal -->
        @if($canAddLibraries)
        <div id="addHelpModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Add Help Type</h3></div>
                <div class="card-body">
                    <form action="{{ route('libraries.help.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Help Type Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                            <button type="button" onclick="document.getElementById('addHelpModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Help Modal -->
        @if($canEditLibraries)
        <div id="editHelpModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Edit Help Type</h3></div>
                <div class="card-body">
                    <form id="editHelpForm" method="POST">
                        @csrf @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Help Type Name</label>
                            <input type="text" id="editHelpName" name="name" class="form-control" required>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Update</button>
                            <button type="button" onclick="document.getElementById('editHelpModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @elseif($tab === 'positions')
        <!-- Positions CRUD -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">Club Positions Management</h3>
                @if($canAddLibraries)
                    <button onclick="document.getElementById('addPositionModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Club Position</button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="library-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positions as $position)
                            <tr>
                                <td>{{ $position->id }}</td>
                                <td style="font-weight: 600;">{{ $position->name }}</td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if($canEditLibraries)
                                            <button 
                                                onclick="editPosition(this)" 
                                                data-id="{{ $position->id }}" 
                                                data-name="{{ $position->name }}"
                                                class="btn btn-outline btn-sm">Edit</button>
                                        @endif
                                        @if($canDeleteLibraries)
                                            <form action="{{ route('libraries.position.destroy', $position) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Position Modal -->
        @if($canAddLibraries)
        <div id="addPositionModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Add Position</h3></div>
                <div class="card-body">
                    <form action="{{ route('libraries.position.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Position Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                            <button type="button" onclick="document.getElementById('addPositionModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Position Modal -->
        @if($canEditLibraries)
        <div id="editPositionModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Edit Position</h3></div>
                <div class="card-body">
                    <form id="editPositionForm" method="POST">
                        @csrf @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Position Name</label>
                            <input type="text" id="editPositionName" name="name" class="form-control" required>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Update</button>
                            <button type="button" onclick="document.getElementById('editPositionModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @elseif($tab === 'regional-positions')
        <!-- Regional Positions CRUD -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">Regional Positions Management</h3>
                @if($canAddLibraries)
                    <button onclick="document.getElementById('addRegionalPositionModal').style.display='flex'" class="btn btn-primary btn-sm">Add Regional Position</button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="library-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regional_positions as $position)
                            <tr>
                                <td>{{ $position->id }}</td>
                                <td style="font-weight: 600;">{{ $position->name }}</td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if($canEditLibraries)
                                            <button 
                                                onclick="editRegionalPosition(this)" 
                                                data-id="{{ $position->id }}" 
                                                data-name="{{ $position->name }}"
                                                class="btn btn-outline btn-sm">Edit</button>
                                        @endif
                                        @if($canDeleteLibraries)
                                            <form action="{{ route('libraries.regional_position.destroy', $position) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Regional Position Modal -->
        @if($canAddLibraries)
        <div id="addRegionalPositionModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Add Regional Position</h3></div>
                <div class="card-body">
                    <form action="{{ route('libraries.regional_position.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Position Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                            <button type="button" onclick="document.getElementById('addRegionalPositionModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Regional Position Modal -->
        @if($canEditLibraries)
        <div id="editRegionalPositionModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Edit Regional Position</h3></div>
                <div class="card-body">
                    <form id="editRegionalPositionForm" method="POST">
                        @csrf @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Position Name</label>
                            <input type="text" id="editRegionalPositionName" name="name" class="form-control" required>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Update</button>
                            <button type="button" onclick="document.getElementById('editRegionalPositionModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @elseif($tab === 'national-officers')
        <!-- National Officers CRUD -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="card-header library-card-header">
                <h3 class="card-title" style="margin-bottom: 0;">National Officers Management</h3>
                @if($canAddLibraries)
                    <button onclick="document.getElementById('addNationalOfficerModal').style.display='flex'" class="btn btn-primary btn-sm">Add New National Officer</button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="library-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Position</th>
                                <th>Fullname</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($national_officers as $officer)
                            <tr>
                                <td>{{ $officer->id }}</td>
                                <td>
                                    @if($officer->photo)
                                        <img src="{{ asset('storage/' . $officer->photo) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: var(--border-color); display: flex; align-items: center; justify-content: center;">
                                            <svg width="20" height="20" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $officer->position }}</td>
                                <td style="font-weight: 600;">{{ $officer->fullname }}</td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        @if($canEditLibraries)
                                            <button 
                                                onclick="editNationalOfficer(this)" 
                                                data-id="{{ $officer->id }}" 
                                                data-position="{{ $officer->position }}"
                                                data-fullname="{{ $officer->fullname }}"
                                                class="btn btn-outline btn-sm">Edit</button>
                                        @endif
                                        @if($canDeleteLibraries)
                                            <form action="{{ route('libraries.national_officer.destroy', $officer) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add National Officer Modal -->
        @if($canAddLibraries)
        <div id="addNationalOfficerModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Add National Officer</h3></div>
                <div class="card-body">
                    <form action="{{ route('libraries.national_officer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" required placeholder="e.g. National President">
                        </div>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Photo (Optional)</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                            <button type="button" onclick="document.getElementById('addNationalOfficerModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit National Officer Modal -->
        @if($canEditLibraries)
        <div id="editNationalOfficerModal" class="library-modal">
            <div class="card library-modal-dialog">
                <div class="card-header"><h3 class="card-title">Edit National Officer</h3></div>
                <div class="card-body">
                    <form id="editNationalOfficerForm" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Position</label>
                            <input type="text" id="editNationalOfficerPosition" name="position" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Full Name</label>
                            <input type="text" id="editNationalOfficerFullname" name="fullname" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label">Photo (Optional, leave blank to keep current)</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Update</button>
                            <button type="button" onclick="document.getElementById('editNationalOfficerModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>

<script>
    function editClub(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const regionId = btn.getAttribute('data-region-id');
        document.getElementById('editClubForm').action = '{{ url("libraries/clubs") }}/' + id;
        document.getElementById('editClubName').value = name;
        document.getElementById('editClubRegionId').value = regionId;
        document.getElementById('editClubModal').style.display = 'flex';
    }
    function editHelp(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        document.getElementById('editHelpForm').action = '{{ url("libraries/help") }}/' + id;
        document.getElementById('editHelpName').value = name;
        document.getElementById('editHelpModal').style.display = 'flex';
    }
    function editPosition(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        document.getElementById('editPositionForm').action = '{{ url("libraries/positions") }}/' + id;
        document.getElementById('editPositionName').value = name;
        document.getElementById('editPositionModal').style.display = 'flex';
    }
    function editRegionalPosition(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        document.getElementById('editRegionalPositionForm').action = '{{ url("libraries/regional-positions") }}/' + id;
        document.getElementById('editRegionalPositionName').value = name;
        document.getElementById('editRegionalPositionModal').style.display = 'flex';
    }
    function editNationalOfficer(btn) {
        const id = btn.getAttribute('data-id');
        const position = btn.getAttribute('data-position');
        const fullname = btn.getAttribute('data-fullname');
        document.getElementById('editNationalOfficerForm').action = '{{ url("libraries/national-officers") }}/' + id;
        document.getElementById('editNationalOfficerPosition').value = position;
        document.getElementById('editNationalOfficerFullname').value = fullname;
        document.getElementById('editNationalOfficerModal').style.display = 'flex';
    }
</script>
@endsection
