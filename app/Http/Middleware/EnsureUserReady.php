<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserReady
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is approved
        if ($user->status !== 1) {
            return redirect()->route('pending');
        }

        // Check if profile is complete
        $isProfileIncomplete = !$user->first_name || 
            !$user->last_name || 
            !$user->sex || 
            !$user->birthday || 
            !$user->marital_status || 
            !$user->address || 
            !$user->location || 
            !$user->contact_number || 
            !$user->contact_person_emergency || 
            !$user->contact_number_emergency || 
            !$user->lib_region_id || 
            !$user->lib_club_name_id || 
            !$user->lib_position_id ||
            !$user->current_job ||
            !$user->office ||
            !$user->profile_photo || 
            !$user->eagle_id_card;

        if ($isProfileIncomplete) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}
