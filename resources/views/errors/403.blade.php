@extends('layouts.app')

@section('title', 'Access Denied - Premium Feature')

@section('content')
@php
    $errorMessage = strtolower($exception->getMessage() ?? '');
    $isUnavailableFeature = str_contains($errorMessage, 'currently unavailable');
    $pendingPayment = auth()->user()->subscriptionPayments()->where('status', 'pending')->latest()->first();
@endphp
<div style="min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div style="background-color: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg); max-width: 600px; width: 100%; overflow: hidden; text-align: center;">
        
        <div style="background-color: rgba(59, 130, 246, 0.05); padding: 3rem 2rem; border-bottom: 1px solid var(--border-color);">
            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.025em;">
                {{ $isUnavailableFeature ? 'This Feature Is Unavailable' : 'This Feature is Locked' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 800px; margin: 0 auto;">
                {{ $isUnavailableFeature ? 'This module is temporarily turned off in system settings.' : 'You do not currently have access to this module.' }}
            </p>
        </div>

        <div style="padding: 2.5rem 2rem;">
            <div style="background-color: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 1.5rem; border-radius: var(--radius-md); text-align: left; margin-bottom: 2rem;">
                <h3 style="color: #d97706; font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $isUnavailableFeature ? 'Feature Disabled in Settings' : 'Yearly Subscription Required' }}
                </h3>
                <p style="color: var(--text-main); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                    @if($isUnavailableFeature)
                        An administrator has temporarily turned off this feature from the Settings page. Please contact the administrator if you need it enabled again.
                    @else
                        Access to this advanced feature requires an active yearly subscription.
                        <br><br>
                        <strong>Important Note:</strong> This subscription fee goes strictly toward the <em>system maintenance costs, server hosting, and technical upkeep</em> of the Caragados Eagles Club web application.
                        <span style="color: #d97706; font-weight: 600;">It is entirely separate and NOT related to any forms of club dues, organizational collections, or internal financial obligations.</span>
                    @endif
                </p>
            </div>

            @if(session('status'))
                <div style="background-color: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--success); padding: 1rem; border-radius: var(--radius-md); text-align: left; margin-bottom: 2rem; color: var(--success);">
                    {{ session('status') }}
                </div>
            @endif

            @if(!$isUnavailableFeature && $pendingPayment)
                <div style="background-color: rgba(59, 130, 246, 0.1); border-left: 4px solid var(--accent); padding: 1.5rem; border-radius: var(--radius-md); text-align: center; margin-bottom: 2rem;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--accent); margin-bottom: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 style="color: var(--accent); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Proof Under Review</h3>
                    <p style="color: var(--text-main); font-size: 0.95rem; margin: 0;">
                        You have successfully submitted your proof of payment. It is currently being reviewed by an administrator. Please check back later.
                    </p>
                </div>
            @elseif(!$isUnavailableFeature)
                <div style="background-color: var(--card-bg); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; text-align: center; margin-bottom: 1.5rem;">
                    <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 1rem;">
                        If you want to avail this feature, simply use the GCash QR code below to make your payment, then upload your transaction receipt here.
                    </p>
                    <img src="{{ asset('storage/gcash.png') }}" alt="GCash QR Code" style="max-width: 250px; height: auto; border-radius: var(--radius-sm); margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
                </div>

                <form action="{{ route('subscription.store') }}" method="POST" enctype="multipart/form-data" style="text-align: left; margin-bottom: 2rem;">
                    @csrf
                    <label for="receipt" style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem;">Upload Proof of Payment (Image only)</label>
                    <input type="file" name="receipt" id="receipt" accept="image/*" required class="form-control" style="margin-bottom: 1rem;">
                    @error('receipt')
                        <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn btn-primary" style="width: 100%; background-color: var(--success); border-color: var(--success);">
                        Submit Payment Proof
                    </button>
                </form>
            @endif

            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="window.history.back()" class="btn btn-outline" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                    Go Back
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding: 0.75rem 1.5rem; font-size: 1rem; text-decoration: none;">
                    Return to Dashboard
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
