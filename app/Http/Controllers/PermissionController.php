<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function update(Request $request, User $user)
    {
        abort_if(!auth()->user()->is_admin, 403);

        $request->validate([
            'is_admin' => 'boolean',
            'access_type_id' => 'nullable|exists:access_types,id',
        ]);

        $user->is_admin = $request->has('is_admin');
        $user->access_type_id = $request->access_type_id;
        $user->save();

        return back()->with('success', 'User access updated successfully.');
    }
}
