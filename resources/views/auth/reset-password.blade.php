@extends('layouts.app')

@section('title', 'Reset Password - Caragados EC')

@section('content')
<style>
    @media (max-width: 576px) {
        .auth-container {
            padding: 0.75rem 0.5rem !important;
            min-height: 90vh !important;
        }
        .card-header-responsive {
            padding: 1.25rem 1rem 1rem 1rem !important;
        }
        .card-header-responsive h1 {
            font-size: 1.25rem !important;
        }
        .card-body-responsive {
            padding: 1.1rem 1rem !important;
        }
    }
</style>

<div class="auth-container" style="min-height: 85vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem 1rem;">
    <div class="card auth-card" style="width: 100%; max-width: 480px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg); background: var(--card-bg); overflow: hidden;">
        
        <!-- Header -->
        <div class="card-header text-center card-header-responsive" style="border-bottom: 1px solid var(--border-color); padding: 1.75rem 1.5rem 1.25rem 1.5rem; background: var(--card-bg);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: var(--success); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="card-title" style="font-size: 1.4rem; font-weight: 800; margin: 0 0 0.25rem 0; color: var(--text-main);">
                Set New Password
            </h1>
            <p class="card-description" style="margin: 0; color: var(--text-muted); font-size: 0.85rem; line-height: 1.5;">
                Please enter your new password below to update your account security.
            </p>
        </div>

        <div class="card-body card-body-responsive" style="padding: 1.5rem 1.75rem;">
            
            @if($errors->any())
                <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: var(--danger); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.88rem;">
                    <ul style="margin: 0; padding-left: 1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div style="margin-bottom: 1.25rem;">
                    <label for="email" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem;">
                        Email Address <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="you@example.com"
                        style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 16px; outline: none;">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="password" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem;">
                        New Password <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Min. 8 characters"
                        style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 16px; outline: none;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="password_confirmation" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem;">
                        Confirm New Password <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat new password"
                        style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 16px; outline: none;">
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width: 100%; justify-content: center; padding: 0.75rem 1.25rem; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); min-height: 44px;">
                    Update Password & Sign In
                </button>
            </form>

        </div>
    </div>
</div>
@endsection
