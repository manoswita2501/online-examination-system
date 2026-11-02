<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - {{ config('app.name', 'Online Examination System') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(145deg, #f4f8f4 0%, #eaf2ea 100%);
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .auth-banner {
            background-color: var(--primary);
            color: #ffffff;
            padding: 24px 28px;
            text-align: center;
        }
        .auth-banner h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .auth-banner p {
            font-size: 13px;
            color: #d1fae5;
        }
        .auth-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            background: #fafcfb;
        }
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .auth-tab.active {
            color: var(--primary);
            background: #ffffff;
            border-bottom: 2px solid var(--primary);
        }
        .auth-tab:hover {
            color: var(--primary);
            text-decoration: none;
        }
        .auth-body {
            padding: 28px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-banner">
                <div style="font-size: 32px; margin-bottom: 8px;">🎓</div>
                <h1>{{ config('app.name', 'Online Examination System') }}</h1>
                <p>University Academic Assessment Portal</p>
            </div>

            <div class="auth-tabs">
                <a href="{{ route('student.login') }}" class="auth-tab {{ request()->routeIs('student.login') ? 'active' : '' }}">
                    Student Portal
                </a>
                <a href="{{ route('admin.login') }}" class="auth-tab {{ request()->routeIs('admin.login') ? 'active' : '' }}">
                    Administrator Portal
                </a>
            </div>

            <div class="auth-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info">
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
