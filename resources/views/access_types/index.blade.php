@extends('layouts.app')

@section('content')
<div style="padding: 2rem; width: 100%; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('dashboard', ['view' => 'admin']) }}" class="btn btn-outline" style="padding: 0.5rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;" title="Back to Administration">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h3 style="font-size: 1.8rem; font-weight: bold; color: var(--text-main); margin: 0;">Access Types <span style="color: var(--accent);">Management</span></h3>
    </div>

    @if(session('success'))
        <div style="background-color: var(--success); color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            {{ session('success') }}
            <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: white; cursor: pointer;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>   
        </div>
    @endif

    <div style="background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2rem;">
        <div style="margin-bottom: 2rem;">
            <label for="accessTypeSelect" style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 1.1rem;">Select Access Type</label>
            <select id="accessTypeSelect" class="form-control" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: var(--radius-md); background-color: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main);">
                <option value="">-- Choose Access Type --</option>
                @foreach($accessTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <form id="permissionsForm" action="" method="POST" style="display: none;">
            @csrf
            
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">Configure Permissions</h3>
            
            <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 2rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); background-color: rgba(0,0,0,0.02);">
                        <th style="padding: 1rem; font-weight: 600;">Module</th>
                        <th style="padding: 1rem; font-weight: 600; text-align: center;">View</th>
                        <th style="padding: 1rem; font-weight: 600; text-align: center;">Add</th>
                        <th style="padding: 1rem; font-weight: 600; text-align: center;">Edit</th>
                        <th style="padding: 1rem; font-weight: 600; text-align: center;">Delete</th>
                    </tr>
                </thead>
                <tbody id="permissionsTableBody">
                    @php
                        $modules = [
                            'dashboard',
                            'announcements',
                            'member_mapping',
                            'alalayang_agila',
                            'search_kuya',
                            'org_structure',
                            'libraries',
                            'users'
                        ];
                    @endphp

                    @foreach($modules as $module)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-size: 1.1rem;">{{ ucwords(str_replace('_', ' ', $module)) }}</td>
                            <td style="padding: 1rem; text-align: center;"><input type="checkbox" name="permissions[{{ $module }}][view]" class="big-checkbox" id="chk_{{ $module }}_view"></td>
                            <td style="padding: 1rem; text-align: center;"><input type="checkbox" name="permissions[{{ $module }}][add]" class="big-checkbox" id="chk_{{ $module }}_add"></td>
                            <td style="padding: 1rem; text-align: center;"><input type="checkbox" name="permissions[{{ $module }}][edit]" class="big-checkbox" id="chk_{{ $module }}_edit"></td>
                            <td style="padding: 1rem; text-align: center;"><input type="checkbox" name="permissions[{{ $module }}][delete]" class="big-checkbox" id="chk_{{ $module }}_delete"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" id="saveButton" class="btn btn-primary" style="background-color: var(--accent); border-color: var(--accent); padding: 0.75rem 2rem; font-size: 1.1rem;">Save Permissions</button>
            </div>
        </form>
        
        <div id="noSelectionMessage" style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 1rem; display: block; opacity: 0.5;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p style="font-size: 1.2rem;">Please select an access type from the dropdown above to configure its permissions.</p>
        </div>
    </div>
</div>

<style>
    /* Styling to make checkboxes big */
    .big-checkbox {
        width: 24px;
        height: 24px;
        cursor: pointer;
        accent-color: var(--accent);
    }
    
    /* Hover effects for rows */
    #permissionsTableBody tr:hover {
        background-color: rgba(0,0,0,0.01);
    }
    
    /* Dropdown focus */
    .form-control:focus {
        border-color: var(--accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare the access types data from backend
        const accessTypesData = {
            @foreach($accessTypes as $type)
            "{{ $type->id }}": {
                name: "{{ $type->name }}",
                permissions: {
                    @foreach($type->permissions as $perm)
                    "{{ $perm->module }}": {
                        view: {{ $perm->allow_view ? 'true' : 'false' }},
                        add: {{ $perm->allow_add ? 'true' : 'false' }},
                        edit: {{ $perm->allow_edit ? 'true' : 'false' }},
                        delete: {{ $perm->allow_delete ? 'true' : 'false' }}
                    },
                    @endforeach
                }
            },
            @endforeach
        };

        const select = document.getElementById('accessTypeSelect');
        const form = document.getElementById('permissionsForm');
        const noSelectionMessage = document.getElementById('noSelectionMessage');
        const saveButton = document.getElementById('saveButton');

        select.addEventListener('change', function() {
            const typeId = this.value;
            
            if (!typeId) {
                form.style.display = 'none';
                noSelectionMessage.style.display = 'block';
                return;
            }

            // Update form display and action
            noSelectionMessage.style.display = 'none';
            form.style.display = 'block';
            form.action = "{{ url('/access-types') }}/" + typeId + "/permissions";
            
            const typeData = accessTypesData[typeId];
            saveButton.textContent = `Save Permissions for ${typeData.name}`;

            // Reset all checkboxes first
            document.querySelectorAll('.big-checkbox').forEach(chk => chk.checked = false);

            // Check boxes based on data
            const perms = typeData.permissions;
            for (const module in perms) {
                if (perms[module].view) document.getElementById(`chk_${module}_view`).checked = true;
                if (perms[module].add) document.getElementById(`chk_${module}_add`).checked = true;
                if (perms[module].edit) document.getElementById(`chk_${module}_edit`).checked = true;
                if (perms[module].delete) document.getElementById(`chk_${module}_delete`).checked = true;
            }
        });
    });
</script>
@endsection
