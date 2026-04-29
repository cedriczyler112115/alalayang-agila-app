<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        }
        catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Failed to authenticate with Google.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // If user doesn't exist, create one.
        if (!$user) {
            $fullName = $googleUser->getName();
            $nameParts = explode(' ', trim($fullName));

            $firstName = $nameParts[0] ?? '';
            $lastName = '';
            $extensionName = '';

            if (count($nameParts) > 1) {
                $lastName = end($nameParts);

                // Check if last part is an extension
                $extensions = ['Jr.', 'Sr.', 'III', 'IV', 'V', 'Jr', 'Sr'];
                if (in_array($lastName, $extensions)) {
                    $extensionName = $lastName;
                    $lastName = $nameParts[count($nameParts) - 2] ?? '';
                }
            }

            $user = User::create([
                'name' => $fullName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'extension_name' => $extensionName,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'status' => 0, // Approve
                'access_type_id' => 5, // Member (Not Availed)
            ]);
        }
        elseif (!$user->google_id) {
            // Link existing account
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
}
