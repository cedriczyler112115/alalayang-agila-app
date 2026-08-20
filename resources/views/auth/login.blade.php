@extends('layouts.app')

@section('title', 'Login & Registration - Caragados EC')

@section('content')
<style>
    .auth-container {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1rem;
    }
    .auth-card-box {
        width: 100%;
        max-width: 520px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-lg);
        background: var(--card-bg);
        overflow: hidden;
    }

    @media (max-width: 576px) {
        .auth-container {
            padding: 0.75rem 0.5rem;
            min-height: 90vh;
        }
        .auth-card-box {
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }
        .card-header-responsive {
            padding: 1.25rem 1rem 1rem 1rem !important;
        }
        .card-header-responsive h1 {
            font-size: 1.35rem !important;
        }
        .card-body-responsive {
            padding: 1.1rem 1rem !important;
        }
        .auth-tab-btn {
            font-size: 0.82rem !important;
            padding: 0.55rem 0.35rem !important;
        }
        .auth-input-field {
            font-size: 16px !important; /* Prevents iOS auto-zoom on input focus */
            padding: 0.7rem 0.85rem !important;
        }
    }
</style>

<div class="auth-container">
    <div class="card auth-card-box">
        
        <!-- Header -->
        <div class="card-header text-center card-header-responsive" style="border-bottom: 1px solid var(--border-color); padding: 1.75rem 1.5rem 1.25rem 1.5rem; background: var(--card-bg);">
            <h1 class="card-title" style="font-size: 1.65rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                CaragaDos <span style="color: var(--accent);">Eagles Club App</span>
            </h1>
            <p class="card-description" style="margin: 0; color: var(--text-muted); font-size: 0.88rem;">
                Caragados Eagles Club, Philippines
            </p>
        </div>

        <div class="card-body card-body-responsive" style="padding: 1.5rem 1.75rem;">
            
            <!-- Global Flash / Status Messages -->
            @if(session('status'))
                <div style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--success); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.88rem; text-align: center; font-weight: 600;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: var(--danger); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.88rem;">
                    <ul style="margin: 0; padding-left: 1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Segmented Auth Mode Switcher -->
            <div style="display: flex; background: rgba(0,0,0,0.06); border-radius: var(--radius-md); padding: 4px; margin-bottom: 1.25rem; gap: 4px; border: 1px solid var(--border-color);">
                <button type="button" id="tabBtnLogin" class="auth-tab-btn" onclick="switchAuthTab('login')"
                    style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.88rem; font-weight: 700; border: none; border-radius: var(--radius-sm); cursor: pointer; transition: all 0.2s ease; background: transparent; color: var(--text-muted);">
                    🔑 Email Login
                </button>
                <button type="button" id="tabBtnRegister" class="auth-tab-btn" onclick="switchAuthTab('register')"
                    style="flex: 1; padding: 0.6rem 0.5rem; font-size: 0.88rem; font-weight: 700; border: none; border-radius: var(--radius-sm); cursor: pointer; transition: all 0.2s ease; background: transparent; color: var(--text-muted);">
                    📝 Register
                </button>
            </div>

            <!-- TAB 1: EMAIL LOGIN FORM -->
            <div id="authPaneLogin" style="display: none;">
                <form method="POST" action="{{ url('/login') }}">
                    @csrf
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label for="login_email" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem;">
                            Email Address <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" id="login_email" name="email" value="{{ old('email') }}" required placeholder="you@example.com" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.92rem; outline: none; transition: border-color 0.2s;">
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap; gap: 4px;">
                            <label for="login_password" style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em;">
                                Password <span style="color: #ef4444;">*</span>
                            </label>
                            <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: var(--accent); font-weight: 600; text-decoration: none;">
                                Forgot Password?
                            </a>
                        </div>
                        <input type="password" id="login_password" name="password" required placeholder="••••••••" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.92rem; outline: none; transition: border-color 0.2s;">
                    </div>

                    <button type="submit" class="btn btn-primary"
                        style="width: 100%; justify-content: center; padding: 0.75rem 1.25rem; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-md); margin-top: 0.5rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); min-height: 44px;">
                        Sign In with Email
                    </button>
                </form>

                <div style="display: flex; align-items: center; margin: 1.25rem 0; gap: 10px;">
                    <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
                </div>

                <a href="{{ route('google.login') }}" class="google-btn" style="width: 100%; justify-content: center; min-height: 44px;">
                    <svg class="google-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="width: 20px; height: 20px; margin-right: 10px;">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    Continue with Google
                </a>
            </div>

            <!-- TAB 2: EMAIL REGISTRATION FORM (1 ROW PER FIELD) -->
            <div id="authPaneRegister" style="display: none;">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- 1. Last Name -->
                    <div style="margin-bottom: 1rem;">
                        <label for="reg_last_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            1. Last Name <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" id="reg_last_name" name="last_name" value="{{ old('last_name') }}" required placeholder="Cruz" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <!-- 2. First Name -->
                    <div style="margin-bottom: 1rem;">
                        <label for="reg_first_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            2. First Name <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" id="reg_first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="Juan" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <!-- 3. Middle Name -->
                    <div style="margin-bottom: 1rem;">
                        <label for="reg_middle_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            3. Middle Name <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">(Optional)</span>
                        </label>
                        <input type="text" id="reg_middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Dela" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <!-- 4. Extension Name -->
                    <div style="margin-bottom: 1rem;">
                        <label for="reg_extension_name" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            4. Extension Name <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">(Optional, e.g. Jr., III)</span>
                        </label>
                        <input type="text" id="reg_extension_name" name="extension_name" value="{{ old('extension_name') }}" placeholder="Jr., III" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <!-- 5. Email Address -->
                    <div style="margin-bottom: 1rem;">
                        <label for="reg_email" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            5. Email Address <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" id="reg_email" name="email" value="{{ old('email') }}" required placeholder="juan.cruz@example.com" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <!-- 6. Password & Verify Password -->
                    <div style="margin-bottom: 1rem;">
                        <label for="reg_password" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            6. Password <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="password" id="reg_password" name="password" required placeholder="Min. 8 characters" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <div style="margin-bottom: 1.35rem;">
                        <label for="reg_password_confirmation" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.35rem;">
                            7. Verify Password <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="password" id="reg_password_confirmation" name="password_confirmation" required placeholder="Repeat password" class="auth-input-field"
                            style="width: 100%; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); font-size: 0.9rem; outline: none;">
                    </div>

                    <button type="submit" class="btn btn-primary"
                        style="width: 100%; justify-content: center; padding: 0.75rem 1.25rem; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); min-height: 44px;">
                        Create Account & Sign In
                    </button>
                </form>
            </div>

            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem; text-align: center; color: var(--text-muted); font-size: 0.82rem;">
                By continuing, you agree to our <a href="#" style="color: var(--accent); text-decoration: none;">Terms of Service</a> and <a href="#" style="color: var(--accent); text-decoration: none;">Privacy Policy</a>.
            </div>

        </div>
    </div>
</div>

<script>
    function switchAuthTab(tab) {
        const btnLogin = document.getElementById('tabBtnLogin');
        const btnRegister = document.getElementById('tabBtnRegister');
        const paneLogin = document.getElementById('authPaneLogin');
        const paneRegister = document.getElementById('authPaneRegister');

        if (tab === 'register') {
            btnRegister.style.background = 'var(--card-bg)';
            btnRegister.style.color = 'var(--accent)';
            btnRegister.style.boxShadow = 'var(--shadow-sm)';

            btnLogin.style.background = 'transparent';
            btnLogin.style.color = 'var(--text-muted)';
            btnLogin.style.boxShadow = 'none';

            paneRegister.style.display = 'block';
            paneLogin.style.display = 'none';
        } else {
            btnLogin.style.background = 'var(--card-bg)';
            btnLogin.style.color = 'var(--accent)';
            btnLogin.style.boxShadow = 'var(--shadow-sm)';

            btnRegister.style.background = 'transparent';
            btnRegister.style.color = 'var(--text-muted)';
            btnRegister.style.boxShadow = 'none';

            paneLogin.style.display = 'block';
            paneRegister.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(old('first_name') || session('activeTab') === 'register' || ($errors->any() && (old('last_name') || old('first_name'))))
            switchAuthTab('register');
        @else
            switchAuthTab('login');
        @endif
    });
</script>
@endsection
