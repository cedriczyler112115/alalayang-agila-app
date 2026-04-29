@extends('layouts.app')

@section('title', 'Pending Approval - Caragados EC')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="card-header text-center" style="border-bottom: none;">
            <div style="width: 64px; height: 64px; background-color: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg width="32" height="32" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="card-title">Pending Approval</h1>
        </div>
        <div class="card-body text-center">
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.6;">
                Your account is currently under review by our administrators. This is to ensure the exclusivity and security of the Caragados Eagles Club. 
                <br><br>
                Please check back later or contact support if you have any questions.
            </p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline" style="width: 100%;">Sign out</button>
            </form>
        </div>
    </div>
</div>
@endsection
