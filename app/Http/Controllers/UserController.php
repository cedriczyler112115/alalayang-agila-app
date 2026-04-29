<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LibRegion;
use App\Models\LibClubName;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['region', 'club']);

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by Region
        if ($request->filled('region_id')) {
            $query->where('lib_region_id', $request->region_id);
        }

        // Filter by Club
        if ($request->filled('club_id')) {
            $query->where('lib_club_name_id', $request->club_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(12)->withQueryString();
        
        $regions = LibRegion::orderBy('name')->get();
        if ($request->filled('region_id')) {
            $clubs = LibClubName::where('lib_region_id', $request->region_id)->orderBy('name')->get();
        } else {
            $clubs = LibClubName::orderBy('name')->get();
        }

        return view('users.index', compact('users', 'regions', 'clubs'));
    }

    public function updateStatus(Request $request, User $user)
    {
        if ($user->status == 0) {
            $user->update([
                'status' => 1, // Active
                'date_approve' => now()
            ]);
            $message = 'User approved and activated successfully.';
        } else {
            $user->update([
                'status' => 0, // Pending
                'date_approve' => null
            ]);
            $message = 'User approval cancelled and set to pending.';
        }

        return back()->with('success', $message);
    }
}
