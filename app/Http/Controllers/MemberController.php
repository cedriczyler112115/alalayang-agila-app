<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\User;
use App\Models\LibRegion;
use App\Models\LibClubName;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MemberController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (!AppSetting::isPremiumFeatureLockEnabled()) {
                    return $next($request);
                }

                $user = auth()->user();
                abort_if(!$user->hasPermission('search_kuya', 'view'), 403, 'Unauthorized action.');
                return $next($request);
            }
        ];
    }

    public function index(Request $request)
    {
        // Base query with mandatory filters: status = 1
        $query = User::query()
            ->with(['region', 'club'])
            ->where('status', 1)
            ->orderBy('last_name', 'asc');

        // Optional Search by name, job, or office
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

        // Optional Filter by Region
        if ($request->filled('region_id')) {
            $query->where('lib_region_id', $request->region_id);
        }

        // Optional Filter by Club
        if ($request->filled('club_id')) {
            $query->where('lib_club_name_id', $request->club_id);
        }

        // Handle per-page selection
        $perPage = $request->get('per_page', 10);
        if ($perPage === 'all') {
            $count = $query->count();
            $members = $query->paginate($count > 0 ? $count : 10)->withQueryString();
        } else {
            $members = $query->paginate((int)$perPage)->withQueryString();
        }

        $regions = LibRegion::all();
        $clubs = LibClubName::all();

        return view('search_kuya', [
            'members' => $members,
            'regions' => $regions,
            'clubs' => $clubs,
        ]);
    }
}
