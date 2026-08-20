<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display login & registration view.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $activeTab = $request->query('tab', 'login');
        return view('auth.login', compact('activeTab'));
    }

    /**
     * Process Email Login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = true; // Set persistent login cookie true

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            session()->flash('from_login', true);
            session()->flash('show_cookie_popup', true);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email')->with('activeTab', 'login');
    }

    /**
     * Process Email Registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'extension_name' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $nameParts = array_filter([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
            $request->extension_name,
        ]);
        $fullName = implode(' ', $nameParts);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'extension_name' => $request->extension_name,
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 0, // Pending Approval
            'access_type_id' => 5, // Member (Not Availed)
        ]);

        // Auto login with remember token
        Auth::login($user, true);
        session()->flash('from_login', true);
        session()->flash('show_cookie_popup', true);

        return redirect('/dashboard');
    }

    /**
     * Show Forgot Password Form.
     */
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot-password');
    }

    /**
     * Send Password Reset Link Email using .env config.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show Reset Password Form.
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Process Reset Password Submission.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user, true);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('from_login', true);
            return redirect('/dashboard')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Store permanent cookie consent & refresh persistent remember token.
     */
    public function acceptCookieRemember(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->setRememberToken(Str::random(60));
            $user->save();
            Auth::login($user, true);
        }

        return response()->json([
            'success' => true,
            'message' => 'Persistent login enabled. Credentials will not expire.'
        ])->cookie(cookie()->forever('cookie_remember_accepted', 'true'));
    }
}
