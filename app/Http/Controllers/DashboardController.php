<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $global_announcements = Announcement::where('status', 'published')
            ->where('scope', 'global')
            ->latest('published_at')
            ->get();

        $regional_announcements = Announcement::where('status', 'published')
            ->where('scope', 'regional')
            ->where('lib_region_id', $user->lib_region_id)
            ->latest('published_at')
            ->get();

        $club_announcements = Announcement::where('status', 'published')
            ->where('scope', 'club')
            ->where('lib_club_name_id', $user->lib_club_name_id)
            ->latest('published_at')
            ->get();

        return view('dashboard', compact('global_announcements', 'regional_announcements', 'club_announcements'));
    }
}
