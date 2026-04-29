<?php

namespace App\Http\Controllers;

use App\Models\AccessType;
use App\Models\AccessTypePermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AccessTypeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                abort_if(!auth()->user()->is_admin, 403, 'Unauthorized action. Admin only.');
                return $next($request);
            }
        ];
    }

    public function index()
    {
        $accessTypes = AccessType::with('permissions')->get();
        return view('access_types.index', compact('accessTypes'));
    }

    public function update(Request $request, AccessType $accessType)
    {
        $request->validate([
            'permissions' => 'array',
        ]);

        if ($request->has('permissions')) {
            foreach ($request->permissions as $module => $perms) {
                AccessTypePermission::updateOrCreate(
                    ['access_type_id' => $accessType->id, 'module' => $module],
                    [
                        'allow_view' => isset($perms['view']),
                        'allow_add' => isset($perms['add']),
                        'allow_edit' => isset($perms['edit']),
                        'allow_delete' => isset($perms['delete']),
                    ]
                );
            }
            
            // Delete permissions that are completely unchecked
            $submittedModules = array_keys($request->permissions);
            AccessTypePermission::where('access_type_id', $accessType->id)
                ->whereNotIn('module', $submittedModules)
                ->delete();
        } else {
            // No permissions sent, meaning all unchecked
            AccessTypePermission::where('access_type_id', $accessType->id)->delete();
        }

        return back()->with('success', "Permissions for {$accessType->name} updated successfully.");
    }
}
