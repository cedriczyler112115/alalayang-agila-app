<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Caragados EC App')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --secondary: #334155;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --danger: #ef4444;
            --success: #22c55e;
            --radius-md: 8px;
            --radius-lg: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            
            /* Professional Header/Footer Colors */
            --header-bg: #0f172a;
            --header-text: #f8fafc;
            --header-muted: #94a3b8;
            --header-border: #1e293b;
            
            --footer-bg: #0f172a;
            --footer-text: #f8fafc;
            --footer-muted: #94a3b8;
            --footer-border: #1e293b;
        }

        /* Dark mode palette */
        @media (prefers-color-scheme: dark) {
            :root {
                --primary: #ffffff;
                --secondary: #cbd5e1;
                --bg-color: #020617;
                --card-bg: #0f172a;
                --text-main: #f8fafc;
                --text-muted: #94a3b8;
                --border-color: #1e293b;

                --header-bg: #020617;
                --header-border: #1e293b;
                --footer-bg: #020617;
                --footer-border: #1e293b;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .navbar {
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--header-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Fix leaflet controls overlapping navbar */
        .leaflet-top, 
        .leaflet-bottom {
            z-index: 999 !important;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--header-text);
            text-decoration: none;
            letter-spacing: -0.025em;
        }

        .navbar-brand span {
            color: var(--accent);
        }

        .navbar-menu {
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: nowrap;
        }

        .nav-link {
            color: var(--header-muted);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            white-space: nowrap;
            padding: 0.5rem 1rem;
            border-radius: 9999px; /* oval/pill shape */
            background-color: transparent;
        }

        .nav-link:hover {
            color: var(--header-text);
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: #ffffff;
            background-color: var(--accent);
        }

        .container {
            flex: 1;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--bg-color);
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background-color: transparent;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background-color: var(--bg-color);
            border-color: var(--text-muted);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--secondary);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background-color: var(--card-bg);
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Cards */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem 1.5rem 0;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .card-description {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .grid { display: grid; gap: 1.5rem; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        /* Layout specific */
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 70px);
            padding: 2rem;
        }

        /* Hamburger Icon Styles */
        .navbar-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 10;
        }

        .navbar-toggle span {
            width: 100%;
            height: 3px;
            background-color: var(--text-muted);
            border-radius: 10px;
            transition: all 0.3s linear;
        }

        /* Animation for hamburger when active */
        .navbar-toggle.active span:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }
        .navbar-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        .navbar-toggle.active span:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }

        @media (max-width: 768px) {
            .grid-cols-2 { grid-template-columns: 1fr; }
            .container { padding: 0.75rem; }
            .navbar { 
                padding: 1rem 0.75rem; 
                flex-direction: column;
                align-items: flex-start;
                gap: 0; /* removed gap when closed */
            }
            .navbar-menu {
                display: none; /* hide by default on mobile */
                width: 100%;
                flex-direction: column;
                gap: 0.5rem;
                padding-top: 1rem;
                padding-bottom: 0.5rem;
            }
            .navbar-menu.show {
                display: flex; /* show when toggled */
            }
            .navbar-toggle {
                display: flex; /* show hamburger on mobile */
            }
            .navbar-brand { font-size: 1.15rem; }
            .auth-wrapper { padding: 0.75rem; }
            .card-body { padding: 1rem; }
            .card-header { padding: 1rem 1rem 0; }
            
            /* Links in mobile menu */
            .nav-link {
                width: 100%;
                border-radius: 9999px; /* keep oval shape */
                padding: 0.75rem 1.25rem;
                text-align: center;
            }
            .navbar-menu form {
                width: 100%;
            }
            .navbar-menu .btn {
                width: 100%;
                padding: 0.75rem 1.25rem;
            }
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            box-shadow: var(--shadow-lg);
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background-color: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .google-btn:hover {
            background-color: var(--bg-color);
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .google-icon {
            width: 20px;
            height: 20px;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 5px; /* space between logo and text */
            text-decoration: none;
        }

        .navbar-brand img {
            display: block;
        }

        .brand-text {
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: block;
            position: absolute;
            right: 0;
            background-color: var(--header-bg);
            min-width: 220px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            border-radius: var(--radius-md);
            border: 1px solid var(--header-border);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            margin-top: 10px;
            overflow: hidden;
        }

        .dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            color: var(--header-muted);
            padding: 0.875rem 1.25rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--header-text);
        }

        .dropdown-item svg {
            color: var(--header-muted);
            transition: color 0.2s ease;
        }

        .dropdown-item:hover svg {
            color: var(--accent);
        }

        .dropdown-item.is-locked {
            opacity: 0.85;
            cursor: pointer;
        }

        .dropdown-item.is-locked:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--header-text);
        }

        .dropdown-item.is-locked svg {
            color: var(--danger);
        }

        .dropdown-item-note {
            margin-left: auto;
            font-size: 0.72rem;
            font-weight: 700;
            color: #fbbf24;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .feature-lock-modal {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.65);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 2000;
        }

        .feature-lock-modal.is-open {
            display: flex;
        }

        .feature-lock-dialog {
            width: 100%;
            max-width: 620px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }

        .feature-lock-header {
            background-color: rgba(59, 130, 246, 0.05);
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .feature-lock-body {
            padding: 1.75rem 1.5rem;
        }

        .feature-lock-alert {
            background-color: rgba(245, 158, 11, 0.1);
            border-left: 4px solid #f59e0b;
            padding: 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
        }

        .feature-lock-note {
            color: #d97706;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-lock-upload {
            background-color: var(--card-bg);
            border: 1px dashed var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .feature-lock-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--header-border);
            margin: 0;
        }

        .user-nav-trigger {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
            white-space: nowrap;
            border-radius: 9999px;
            color: var(--header-text);
        }

        .user-nav-trigger:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Footer Styles */
        .footer {
            background-color: var(--footer-bg);
            border-top: 1px solid var(--footer-border);
            padding: 5rem 2rem 3rem;
            margin-top: auto;
            color: var(--footer-text);
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1.5fr;
            gap: 4rem;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--footer-text);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .footer-logo span {
            color: var(--accent);
        }

        .footer-description {
            color: var(--footer-muted);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 400px;
        }

        .footer-links-column h4,
        .footer-contact-column h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--footer-text);
            position: relative;
            padding-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-links-column h4::after,
        .footer-contact-column h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--accent);
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-link {
            color: var(--footer-muted);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-link:hover {
            color: var(--header-text);
            transform: translateX(5px);
        }

        .footer-contact-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .contact-item {
            display: flex;
            gap: 14px;
            color: var(--footer-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .contact-icon {
            color: var(--accent);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .footer-bottom {
            margin-top: 5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--footer-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--footer-muted);
            font-size: 0.9rem;
        }

        .footer-socials {
            display: flex;
            gap: 1.25rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--footer-muted);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--footer-border);
        }

        .social-link:hover {
            background-color: var(--accent);
            color: #ffffff;
            border-color: var(--accent);
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        @media (max-width: 992px) {
            .footer-container {
                grid-template-columns: 1.5fr 1fr;
                gap: 3rem;
            }
            .footer-contact-column {
                grid-column: span 2;
            }
        }

        @media (max-width: 768px) {
            .footer {
                padding: 3rem 1.5rem 1.5rem;
            }
            .footer-container {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
            .footer-contact-column {
                grid-column: span 1;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
                margin-top: 3rem;
            }
        }
        /* Global Loader Styles */
        .global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: var(--bg-color);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.3s ease;
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .circular-spinner {
            width: 120px;
            height: 120px;
            animation: rotate 2s linear infinite;
            position: absolute;
            top: 0;
            left: 0;
        }

        .circular-spinner circle {
            stroke: var(--accent);
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0;
            animation: dash 1.5s ease-in-out infinite;
            stroke-linecap: round;
        }

        .loader-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .loader-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
            letter-spacing: 0.5px;
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            text-align: center;
        }

        @keyframes rotate { 100% { transform: rotate(360deg); } }
        @keyframes dash {
            0% { stroke-dasharray: 1, 200; stroke-dashoffset: 0; }
            50% { stroke-dasharray: 90, 200; stroke-dashoffset: -35px; }
            100% { stroke-dasharray: 90, 200; stroke-dashoffset: -124px; }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .6; }
        }
    </style>
</head>
<body>
    @auth
        @php
            $featureLockPendingPayment = auth()->user()->subscriptionPayments()->where('status', 'pending')->latest()->first();
        @endphp
    @endauth
    
    <!-- Global Full-Screen Loader -->
    <div id="global-loader" class="global-loader">
        <div class="loader-content">
            <div style="position: relative; width: 120px; height: 120px;">
                <svg class="circular-spinner" viewBox="25 25 50 50">
                    <circle cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/>
                </svg>
                <div class="loader-icon">
                    <img src="{{ asset('storage/eaglelogo.png') }}" width="80" alt="Icon">
                </div>
            </div>
            <p id="loader-text" class="loader-text">Loading application...</p>
        </div>
    </div>

    @auth
    <nav class="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" width="45" height="45">
                CaragaDos<span>EaglesClub</span>
            </a>
            
            <button class="navbar-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="navbar-menu" id="navMenu">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            
            <div class="dropdown">
                <div class="nav-link {{ request()->routeIs('profile.location') || request()->routeIs('quick.response') || request()->routeIs('search.kuya') || request()->routeIs('chat.*') || request()->routeIs('announcements.*') ? 'active' : '' }}" style="cursor: pointer; display: flex; align-items: center; gap: 4px;">
                    Services
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="dropdown-content">
                    <a href="{{ route('profile.location') }}" class="dropdown-item">
                        Member Mapping
                    </a>
                    <a href="{{ route('quick.response') }}" class="dropdown-item">
                        Alalayang Agila Help
                    </a>
                    <a href="{{ route('search.kuya') }}" class="dropdown-item">
                        Find A Kuya
                    </a>
                    @if(\App\Models\AppSetting::isChatWithKuyaEnabled())
                    <a href="{{ route('chat.index') }}" class="dropdown-item">
                        Chat with Kuya
                    </a>
                    @endif
                    <a href="{{ route('announcements.index') }}" class="dropdown-item">
                        Publish Announcement
                    </a>
                </div>
            </div>
            
            <a href="{{ route('org.structure') }}" class="nav-link {{ request()->routeIs('org.structure') ? 'active' : '' }}">Organizational Structure</a>
            
            @if(auth()->user()->is_admin)
            <a href="{{ route('libraries.index') }}" class="nav-link {{ request()->routeIs('libraries.index') ? 'active' : '' }}">Libraries</a>
            
            <div class="dropdown">
                <div class="nav-link {{ request()->routeIs('users.*') || request()->routeIs('access_types.*') || request()->routeIs('settings.*') ? 'active' : '' }}" style="cursor: pointer; display: flex; align-items: center; gap: 4px;">
                    Administration
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="dropdown-content">
                    <a href="{{ route('users.index') }}" class="dropdown-item">
                        Users
                    </a>
                    <a href="{{ route('access_types.index') }}" class="dropdown-item">
                        Access Types
                    </a>
                    <a href="{{ route('settings.index') }}" class="dropdown-item">
                        Settings
                    </a>
                </div>
            </div>
            @endif

            <!-- User Dropdown -->
            <div class="dropdown">
                <div class="user-nav-trigger">
                    <span style="color: var(--text-muted); font-weight: 600;">
                        <span style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem; font-weight: 400;">Kuya</span>, {{ auth()->user()->fullname }}
                    </span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="dropdown-content">
                    <a href="{{ route('profile.complete') }}" class="dropdown-item">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        My Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                        @csrf
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--danger);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="container">
        @yield('content')
    </main>

    @auth
    <div id="featureLockModal" class="feature-lock-modal" aria-hidden="true">
        <div class="feature-lock-dialog">
            <div class="feature-lock-header">
                <div style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">This Feature is Locked</h2>
                <p id="featureLockDescription" style="color: var(--text-muted); font-size: 1rem; margin: 0;">You do not currently have access to this module.</p>
            </div>
            <div class="feature-lock-body">
                <div class="feature-lock-alert">
                    <div class="feature-lock-note">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Yearly Subscription Required
                    </div>
                    <p style="color: var(--text-main); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        Access to this advanced feature requires an active yearly subscription.
                        <br><br>
                        <strong>Important Note:</strong> This subscription fee goes strictly toward the <em>system maintenance costs, server hosting, and technical upkeep</em> of the Caragados Eagles Club web application.
                        <span style="color: #d97706; font-weight: 600;">It is entirely separate and NOT related to any forms of club dues, organizational collections, or internal financial obligations.</span>
                    </p>
                </div>

                @if(session('status'))
                    <div style="background-color: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--success); padding: 1rem; border-radius: var(--radius-md); text-align: left; margin-bottom: 1.5rem; color: var(--success);">
                        {{ session('status') }}
                    </div>
                @endif

                @if($featureLockPendingPayment)
                    <div style="background-color: rgba(59, 130, 246, 0.1); border-left: 4px solid var(--accent); padding: 1.5rem; border-radius: var(--radius-md); text-align: center; margin-bottom: 1.5rem;">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--accent); margin-bottom: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 style="color: var(--accent); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Proof Under Review</h3>
                        <p style="color: var(--text-main); font-size: 0.95rem; margin: 0;">
                            You have successfully submitted your proof of payment. It is currently being reviewed by an administrator. Please check back later.
                        </p>
                    </div>
                @else
                    <div class="feature-lock-upload">
                        <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 1rem;">
                            If you want to avail this feature, simply use the GCash QR code below to make your payment, then upload your transaction receipt here.
                        </p>
                        <img src="{{ asset('storage/gcash.png') }}" alt="GCash QR Code" style="max-width: 250px; width: 100%; height: auto; border-radius: var(--radius-sm); margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
                    </div>

                    <form action="{{ route('subscription.store') }}" method="POST" enctype="multipart/form-data" style="text-align: left; margin-bottom: 1.5rem;">
                        @csrf
                        <label for="featureLockReceipt" style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem;">Upload Proof of Payment (Image only)</label>
                        <input type="file" name="receipt" id="featureLockReceipt" accept="image/*" required class="form-control" style="margin-bottom: 1rem;">
                        @error('receipt')
                            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                        @enderror
                        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: var(--success); border-color: var(--success);">
                            Submit Payment Proof
                        </button>
                    </form>
                @endif

                <div class="feature-lock-actions">
                    <button type="button" id="featureLockCloseButton" class="btn btn-outline">Close</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="text-decoration: none;">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <a href="{{ route('dashboard') }}" class="footer-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="50" height="50">
                    CaragaDos<span>EaglesClub</span>
                </a>
                <p class="footer-description">
                    The CaragaDos Eagles Club is a community-driven organization dedicated to service, brotherhood, and emergency response. We empower our members to make a difference in the Caraga region.
                </p>
                <div class="footer-socials">
                    <a href="#" class="social-link" title="Facebook">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3l-.5 3h-2.5v6.8c4.56-.93 8-4.96 8-9.8z"/></svg>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2h10c2.76 0 5 2.24 5 5v10c0 2.76-2.24 5-5 5H7c-2.76 0-5-2.24-5-5V7c0-2.76 2.24-5 5-5zm10 2H7c-1.66 0-3 1.34-3 3v10c0 1.66 1.34 3 3 3h10c1.66 0 3-1.34 3-3V7c0-1.66-1.34-3-3-3zM12 7c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM18 5.25c.41 0 .75.34.75.75s-.34.75-.75.75-.75-.34-.75-.75.34-.75.75-.75z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-links-column">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a></li>
                    <li><a href="{{ route('announcements.index') }}" class="footer-link">Announcements</a></li>
                    <li><a href="{{ route('profile.location') }}" class="footer-link">Kuya Mapping</a></li>
                    <li><a href="{{ route('quick.response') }}" class="footer-link">Alalayang Agila Help</a></li>
                    <li><a href="{{ route('search.kuya') }}" class="footer-link">Find A Kuya</a></li>
                </ul>
            </div>

            <div class="footer-contact-column">
                <h4>Contact Info</h4>
                <div class="footer-contact-info">
                    <div class="contact-item">
                        <svg class="contact-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Butuan City, Caraga Region, Philippines</span>
                    </div>
                    <div class="contact-item">
                        <svg class="contact-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>info@caragados-eagles.org</span>
                    </div>
                    <div class="contact-item">
                        <svg class="contact-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>+63 900 000 0000</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} CaragaDos Eagles Club. All rights reserved.</p>
            <p>Built with ❤️ for the Brotherhood</p>
        </div>
    </footer>

    <script>
        window.GlobalLoader = {
            show: function(text = "Loading...") {
                const loader = document.getElementById('global-loader');
                const textEl = document.getElementById('loader-text');
                if (loader && textEl) {
                    textEl.innerText = text;
                    loader.style.display = 'flex';
                    loader.style.opacity = '1';
                    document.body.style.overflow = 'hidden';
                }
            },
            hide: function() {
                const loader = document.getElementById('global-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                        document.body.style.overflow = '';
                    }, 300); // Wait for fade out
                }
            }
        };

        // Prevent body scrolling initially
        document.body.style.overflow = 'hidden';

        // Hide loader on initial page load
        window.addEventListener('load', function() {
            GlobalLoader.hide();
        });

        // Show loader on form submits
        document.addEventListener('submit', function(e) {
            if (!e.defaultPrevented) {
                GlobalLoader.show("Processing your request...");
            }
        });

        // Show loader on local navigation
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                const isHash = link.getAttribute('href') && link.getAttribute('href').startsWith('#');
                const isTargetBlank = link.target === '_blank';
                const isDownload = link.hasAttribute('download');
                const isJs = link.href.startsWith('javascript:');
                
                if (!isHash && !isTargetBlank && !isDownload && !isJs && !e.defaultPrevented) {
                    if (link.hostname === window.location.hostname) {
                        GlobalLoader.show("Navigating...");
                    }
                }
            }
        });

        // Intercept XMLHttpRequest (AJAX/jQuery)
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url) {
            const isChatPoller = typeof url === 'string' && url.includes('/chat/');
            if (!isChatPoller) {
                this.addEventListener('loadstart', function() { GlobalLoader.show("Loading data..."); });
                this.addEventListener('loadend', function() { GlobalLoader.hide(); });
                this.addEventListener('error', function() { GlobalLoader.hide(); });
                this.addEventListener('abort', function() { GlobalLoader.hide(); });
            }
            originalOpen.apply(this, arguments);
        };
        
        // Intercept window.fetch
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            let fetchUrl = '';
            if (typeof args[0] === 'string') {
                fetchUrl = args[0];
            } else if (args[0] && args[0].url) {
                fetchUrl = args[0].url;
            }
            const isChatPoller = fetchUrl.includes('/chat/');

            if (!isChatPoller) {
                GlobalLoader.show("Fetching data...");
            }
            try {
                const response = await originalFetch(...args);
                return response;
            } finally {
                if (!isChatPoller) {
                    GlobalLoader.hide();
                }
            }
        };

        // Handle browser back button (bfcache)
        window.addEventListener('pageshow', function(event) {
            // Hide the loader whenever the page is shown, especially if restored from bfcache
            GlobalLoader.hide();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');
            const featureLockModal = document.getElementById('featureLockModal');
            const featureLockDescription = document.getElementById('featureLockDescription');
            const featureLockCloseButton = document.getElementById('featureLockCloseButton');

            function openFeatureLockModal(featureName = 'This feature') {
                if (!featureLockModal) {
                    return;
                }

                if (featureLockDescription) {
                    featureLockDescription.textContent = `${featureName} requires an active subscription before you can use it.`;
                }

                featureLockModal.classList.add('is-open');
                featureLockModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeFeatureLockModal() {
                if (!featureLockModal) {
                    return;
                }

                featureLockModal.classList.remove('is-open');
                featureLockModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    this.classList.toggle('active');
                    navMenu.classList.toggle('show');
                });
            }

            document.querySelectorAll('.open-feature-lock').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    openFeatureLockModal(this.getAttribute('data-feature-name') || 'This feature');
                });
            });

            featureLockCloseButton?.addEventListener('click', closeFeatureLockModal);

            featureLockModal?.addEventListener('click', function(event) {
                if (event.target === featureLockModal) {
                    closeFeatureLockModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeFeatureLockModal();
                }
            });

            @if($errors->has('receipt'))
                openFeatureLockModal('This feature');
            @endif
        });
    </script>
</body>
</html>
