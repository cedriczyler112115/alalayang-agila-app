<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\LibRegion;
use App\Models\LibClubName;
use App\Models\LibPosition;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProfileController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (!AppSetting::isPremiumFeatureLockEnabled()) {
                    return $next($request);
                }

                if ($request->route()->getActionMethod() === 'location') {
                    abort_if(!auth()->user()->hasPermission('member_mapping', 'view'), 403, 'Unauthorized action.');
                }
                return $next($request);
            }
        ];
    }

    public function edit()
    {
        return view('profile.complete', [
            'user' => request()->user(),
            'regions' => LibRegion::all(),
            'clubs' => LibClubName::all(),
            'clubs_by_region' => LibClubName::all()->groupBy('lib_region_id'),
            'positions' => LibPosition::all(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'birthday' => 'required|date',
            'marital_status' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location' => 'required|string', // Location from map is now mandatory
            'contact_number' => 'required|string|max:255',
            'contact_person_emergency' => 'required|string|max:255',
            'contact_number_emergency' => 'required|string|max:255',
            'lib_region_id' => 'required|exists:lib_region,id',
            'lib_club_name_id' => 'required|exists:lib_club_name,id',
            'lib_position_id' => 'required|exists:lib_position,id',
            'current_job' => 'required|string|max:255',
            'office' => 'required|string|max:255',
            'make_private' => 'nullable', // Will be converted to boolean below
            'profile_photo' => ($user->profile_photo ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif',
            'eagle_id_card' => ($user->eagle_id_card ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif',
        ]);

        $validated['make_private'] = $request->has('make_private');

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        if ($request->hasFile('eagle_id_card')) {
            $path = $request->file('eagle_id_card')->store('eagle-ids', 'public');
            $validated['eagle_id_card'] = $path;
        }

        // Update the legacy name field too
        $fullName = "{$validated['last_name']}, {$validated['first_name']} {$validated['middle_name']}";
        if ($validated['extension_name']) {
            $fullName .= " {$validated['extension_name']}";
        }
        $validated['name'] = trim($fullName);

        $user->update($validated);

        return redirect('/dashboard')->with('status', 'Profile completed!');
    }

    public function location()
    {
        $members = \App\Models\User::whereNotNull('location')
            ->where('make_private', false)
            ->with(['region', 'club'])
            ->get(['first_name', 'last_name', 'middle_name', 'extension_name', 'location', 'lib_region_id', 'lib_club_name_id', 'profile_photo', 'eagle_id_card']);

        return view('profile.location', [
            'user' => request()->user(),
            'members' => $members
        ]);
    }
}
