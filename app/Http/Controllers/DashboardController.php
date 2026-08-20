<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\LibTelegram;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [];
    }

    public function index()
    {
        $user = auth()->user();

        $global_announcements = Announcement::where('status', 'published')
            ->where('scope', 'global')
            ->with(['user.region', 'user.club', 'user.position'])
            ->latest('published_at')
            ->get();

        $regional_announcements = Announcement::where('status', 'published')
            ->where('scope', 'regional')
            ->where('lib_region_id', $user->lib_region_id)
            ->with(['user.region', 'user.club', 'user.position'])
            ->latest('published_at')
            ->get();

        $club_announcements = Announcement::where('status', 'published')
            ->where('scope', 'club')
            ->where('lib_club_name_id', $user->lib_club_name_id)
            ->with(['user.region', 'user.club', 'user.position'])
            ->latest('published_at')
            ->get();

        $userTelegramLink = null;
        $userClubName = null;
        $userClubKeyword = null;
        if ($user->lib_club_name_id) {
            $userTelegramLink = LibTelegram::where('club_id', $user->lib_club_name_id)->value('link');
            $club = $user->club ?? \App\Models\LibClubName::find($user->lib_club_name_id);
            $userClubName = $club?->name;
            $userClubKeyword = $club?->notification_keyword;
        }

        $userRegionKeyword = null;
        if ($user->lib_region_id) {
            $userRegionKeyword = $user->region?->notification_keyword ?? \App\Models\LibRegion::where('id', $user->lib_region_id)->value('notification_keyword');
        }

        return view('dashboard', compact('global_announcements', 'regional_announcements', 'club_announcements'));
    }

    public function notificationsSetup()
    {
        $user = auth()->user();

        $userTelegramLink = null;
        $userClubName = null;
        $userClubKeyword = null;
        if ($user->lib_club_name_id) {
            $userTelegramLink = LibTelegram::where('club_id', $user->lib_club_name_id)->value('link');
            $club = $user->club ?? \App\Models\LibClubName::find($user->lib_club_name_id);
            $userClubName = $club?->name;
            $userClubKeyword = $club?->notification_keyword;
        }

        $userRegionKeyword = null;
        if ($user->lib_region_id) {
            $userRegionKeyword = $user->region?->notification_keyword ?? \App\Models\LibRegion::where('id', $user->lib_region_id)->value('notification_keyword');
        }

        return view('notifications.setup', compact('userTelegramLink', 'userClubName', 'userClubKeyword', 'userRegionKeyword'));
    }
}
