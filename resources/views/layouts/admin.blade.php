<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Online Examination System') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Print-only header for formal reports -->
    <div class="print-only-header">
        <div class="print-college-title">{{ config('app.name', 'Online Examination System') }}</div>
        <div class="print-sub-title">Office of the Controller of Examinations — Official Academic Report</div>
        <div style="font-size: 10pt; color: #666; margin-top: 4px;">Generated on: {{ now()->format('d M Y, h:i A') }}</div>
    </div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar no-print">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <span>🏛️</span>
                    <span>Exam Portal</span>
                </div>
                <div class="sidebar-subtitle">Admin Control Center</div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.batches.index') }}" class="{{ request()->routeIs('admin.batches.*') ? 'active' : '' }}">
                        <span>📚</span>
                        <span>Batches</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <span>👨‍🎓</span>
                        <span>Students</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.exams.index') }}" class="{{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                        <span>📝</span>
                        <span>Examinations</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <span>📈</span>
                        <span>Results & Reports</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div>Admin ID: <strong>{{ Auth::user()->admin_id ?? '01234' }}</strong></div>
                <div style="font-size: 11px; margin-top: 4px; opacity: 0.8;">Laravel 12 & MySQL</div>
            </div>
        </aside>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Topbar -->
            <header class="topbar no-print">
                <div class="topbar-title">@yield('page_title', 'Administrator Panel')</div>

                <div class="topbar-actions">
                    <div class="user-badge">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-name">{{ Auth::user()->name }}</div>
                            <div class="user-info-role">Administrator</div>
                        </div>
                    </div>

                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" title="Log Out">
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Body -->
            <main class="content-body">
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
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
