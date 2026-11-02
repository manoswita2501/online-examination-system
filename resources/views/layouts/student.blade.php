<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - {{ config('app.name', 'Online Examination System') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Print-only header for formal reports -->
    <div class="print-only-header">
        <div class="print-college-title">{{ config('app.name', 'Online Examination System') }}</div>
        <div class="print-sub-title">Student Examination Grade Sheet & Performance Report</div>
        <div style="font-size: 10pt; color: #666; margin-top: 4px;">Generated on: {{ now()->format('d M Y, h:i A') }}</div>
    </div>

    @sectionMissing('distraction_free')
    <!-- Standard Student Navbar -->
    <header class="student-navbar no-print">
        <div class="navbar-inner">
            <a href="{{ route('student.dashboard') }}" class="navbar-brand">
                <span>🎓</span>
                <span>{{ config('app.name', 'Online Examination System') }}</span>
            </a>

            <div class="navbar-links">
                <a href="{{ route('student.dashboard') }}" class="navbar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    My Dashboard
                </a>

                <div class="user-badge" style="border-left: 1px solid rgba(255,255,255,0.2); padding-left: 14px;">
                    <div class="user-avatar" style="background: rgba(255,255,255,0.2); color: #ffffff; border-color: rgba(255,255,255,0.3);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info-text">
                        <div class="user-info-name" style="color: #ffffff;">{{ Auth::user()->name }}</div>
                        <div class="user-info-role" style="color: #a7f3d0;">ID: {{ Auth::user()->student_id }}</div>
                    </div>
                </div>

                <form action="{{ route('student.logout') }}" method="POST" style="margin-left: 8px;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="color: #ffffff; border-color: rgba(255,255,255,0.4);">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endif

    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 24px 20px;">
        @if (session('success'))
            <div class="alert alert-success no-print">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger no-print">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info no-print">
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
