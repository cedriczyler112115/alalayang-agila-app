@extends('layouts.app')

@section('title', 'Forgot Password - Caragados EC')

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
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h1 class="card-title" style="font-size: 1.4rem; font-weight: 800; margin: 0 0 0.25rem 0; color: var(--text-main);">
                Forgot Your Password?
            </h1>
            <p class="card-description" style="margin: 0; color: var(--text-muted); font-size: 0.85rem; line-height: 1.5;">
                Enter your registered email address and we'll send you a password reset link.
            </p>
        </div>

        <div class="card-body card-body-responsive" style="padding: 1.5rem 1.75rem;">
            
            @if(session('status'))
                <div style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--success); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.88rem; text-align: center; font-weight: 600;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: var(--danger); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.88rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div style="margin-bottom: 1.5rem;">
                    <label for="email" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem;">
                        Email Address <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com"
                        style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 16px; outline: none; transition: border-color 0.2s;">
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width: 100%; justify-content: center; padding: 0.75rem 1.25rem; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); min-height: 44px;">
                    Send Password Reset Link
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="{{ route('login') }}" style="font-size: 0.85rem; color: var(--accent); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Login
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
