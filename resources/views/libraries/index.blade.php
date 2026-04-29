@extends('layouts.app')

@section('title', 'Libraries - Caragados EC')

@section('content')
<div style="margin-top: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em;">Dynamic <span style="color: var(--accent);">Libraries</span></h1>
    </div>

    @if(session('status'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; flex-wrap: wrap;">
        <a href="{{ route('libraries.index', ['tab' => 'regions']) }}" class="nav-link {{ $tab === 'regions' ? 'active' : '' }}" style="border-radius: 9999px; padding: 0.5rem 1.5rem;">Regions / Chapters</a>
        <a href="{{ route('libraries.index', ['tab' => 'regional-positions']) }}" class="nav-link {{ $tab === 'regional-positions' ? 'active' : '' }}" style="border-radius: 9999px; padding: 0.5rem 1.5rem;">Regional Positions</a>
        <a href="{{ route('libraries.index', ['tab' => 'clubs']) }}" class="nav-link {{ $tab === 'clubs' ? 'active' : '' }}" style="border-radius: 9999px; padding: 0.5rem 1.5rem;">Eagle Club Names</a>
        <a href="{{ route('libraries.index', ['tab' => 'help']) }}" class="nav-link {{ $tab === 'help' ? 'active' : '' }}" style="border-radius: 9999px; padding: 0.5rem 1.5rem;">Help Types</a>
        <a href="{{ route('libraries.index', ['tab' => 'positions']) }}" class="nav-link {{ $tab === 'positions' ? 'active' : '' }}" style="border-radius: 9999px; padding: 0.5rem 1.5rem;">Club Positions</a>
        <a href="{{ route('libraries.index', ['tab' => 'national-officers']) }}" class="nav-link {{ $tab === 'national-officers' ? 'active' : '' }}" style="border-radius: 9999px; padding: 0.5rem 1.5rem;">National Officers</a>
    </div>

    @if($tab === 'regions')
        <!-- Regions CRUD -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Regions Management</h3>
                <button onclick="document.getElementById('addRegionModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Region</button>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regions as $region)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">{{ $region->id }}</td>
                            <td style="padding: 1rem;">{{ $region->name }}</td>
                            <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('libraries.region.edit', $region->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form action="{{ route('libraries.region.destroy', $region) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Region Modal -->
        <div id="addRegionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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

        <!-- Edit Region moved to dedicated page -->
    @elseif($tab === 'clubs')
        <!-- Clubs CRUD -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Eagle Clubs Management</h3>
                <button onclick="document.getElementById('addClubModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Club</button>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Region</th>
                            <th style="padding: 1rem;">Club Name</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clubs as $club)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">{{ $club->id }}</td>
                            <td style="padding: 1rem;">{{ $club->region->name ?? 'N/A' }}</td>
                            <td style="padding: 1rem;">{{ $club->name }}</td>
                            <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('libraries.club.edit', $club->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form action="{{ route('libraries.club.destroy', $club) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Club Modal -->
        <div id="addClubModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                            <button type="button" onclick="document.getElementById('addClubModal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @elseif($tab === 'help')
        <!-- Help Types CRUD -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Help Types Management</h3>
                <button onclick="document.getElementById('addHelpModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Help Type</button>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($help_types as $help)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">{{ $help->id }}</td>
                            <td style="padding: 1rem;">{{ $help->name }}</td>
                            <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button 
                                    onclick="editHelp(this)" 
                                    data-id="{{ $help->id }}" 
                                    data-name="{{ $help->name }}"
                                    class="btn btn-outline btn-sm">Edit</button>
                                <form action="{{ route('libraries.help.destroy', $help) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Help Modal -->
        <div id="addHelpModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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

        <!-- Edit Help Modal -->
        <div id="editHelpModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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
    @elseif($tab === 'positions')
        <!-- Positions CRUD -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Club Positions Management</h3>
                <button onclick="document.getElementById('addPositionModal').style.display='flex'" class="btn btn-primary btn-sm">Add New Club Position</button>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">{{ $position->id }}</td>
                            <td style="padding: 1rem;">{{ $position->name }}</td>
                            <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button 
                                    onclick="editPosition(this)" 
                                    data-id="{{ $position->id }}" 
                                    data-name="{{ $position->name }}"
                                    class="btn btn-outline btn-sm">Edit</button>
                                <form action="{{ route('libraries.position.destroy', $position) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Position Modal -->
        <div id="addPositionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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

        <!-- Edit Position Modal -->
        <div id="editPositionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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
    @elseif($tab === 'regional-positions')
        <!-- Regional Positions CRUD -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Regional Positions Management</h3>
                <button onclick="document.getElementById('addRegionalPositionModal').style.display='flex'" class="btn btn-primary btn-sm">Add Regional Position</button>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regional_positions as $position)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">{{ $position->id }}</td>
                            <td style="padding: 1rem;">{{ $position->name }}</td>
                            <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button 
                                    onclick="editRegionalPosition(this)" 
                                    data-id="{{ $position->id }}" 
                                    data-name="{{ $position->name }}"
                                    class="btn btn-outline btn-sm">Edit</button>
                                <form action="{{ route('libraries.regional_position.destroy', $position) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Regional Position Modal -->
        <div id="addRegionalPositionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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

        <!-- Edit Regional Position Modal -->
        <div id="editRegionalPositionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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
    @elseif($tab === 'national-officers')
        <!-- National Officers CRUD -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">National Officers Management</h3>
                <button onclick="document.getElementById('addNationalOfficerModal').style.display='flex'" class="btn btn-primary btn-sm">Add New National Officer</button>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Photo</th>
                            <th style="padding: 1rem;">Position</th>
                            <th style="padding: 1rem;">Fullname</th>
                            <th style="padding: 1rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($national_officers as $officer)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">{{ $officer->id }}</td>
                            <td style="padding: 1rem;">
                                @if($officer->photo)
                                    <img src="{{ asset('storage/' . $officer->photo) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: var(--border-color); display: flex; align-items: center; justify-content: center;">
                                        <svg width="20" height="20" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 1rem;">{{ $officer->position }}</td>
                            <td style="padding: 1rem; font-weight: 600;">{{ $officer->fullname }}</td>
                            <td style="padding: 1rem; text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button 
                                    onclick="editNationalOfficer(this)" 
                                    data-id="{{ $officer->id }}" 
                                    data-position="{{ $officer->position }}"
                                    data-fullname="{{ $officer->fullname }}"
                                    class="btn btn-outline btn-sm">Edit</button>
                                <form action="{{ route('libraries.national_officer.destroy', $officer) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add National Officer Modal -->
        <div id="addNationalOfficerModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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

        <!-- Edit National Officer Modal -->
        <div id="editNationalOfficerModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 400px; margin: 1rem;">
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
</div>

<script>
    function editClub(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const regionId = btn.getAttribute('data-region-id');
        // Use relative path or dynamic base URL to avoid 404 in subdirectories
        document.getElementById('editClubForm').action = '{{ url("libraries/clubs") }}/' + id;
        document.getElementById('editClubName').value = name;
        document.getElementById('editClubRegionId').value = regionId;
        document.getElementById('editClubModal').style.display = 'flex';
    }
    function editHelp(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        // Use relative path or dynamic base URL to avoid 404 in subdirectories
        document.getElementById('editHelpForm').action = '{{ url("libraries/help") }}/' + id;
        document.getElementById('editHelpName').value = name;
        document.getElementById('editHelpModal').style.display = 'flex';
    }
    function editPosition(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        // Use relative path or dynamic base URL to avoid 404 in subdirectories
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
