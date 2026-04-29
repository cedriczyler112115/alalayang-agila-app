<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LibRegion;
use App\Models\LibClubName;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with(['region', 'club']);

        // Search by name, job, or office
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('current_job', 'like', "%{$search}%")
                  ->orWhere('office', 'like', "%{$search}%");
            });
        }

        // Exclude private profiles
        $query->where('make_private', false);

        // Filter by Region
        if ($request->filled('region_id')) {
            $query->where('lib_region_id', $request->region_id);
        }

        // Filter by Club
        if ($request->filled('club_id')) {
            $query->where('lib_club_name_id', $request->club_id);
        }

        // Only approved users (status = 1) - optional, but usually better
        // $query->where('status', 1);

        $members = $query->paginate(12)->withQueryString();
        $regions = LibRegion::all();
        $clubs = LibClubName::all();

        return view('search_kuya', [
            'members' => $members,
            'regions' => $regions,
            'clubs' => $clubs,
        ]);
    }
}
