<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Online Examination System') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .hero-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(160deg, #f3f8f3 0%, #e5efe5 100%);
        }
        .hero-nav {
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(8px);
        }
        .hero-brand {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hero-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .portal-grid {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 28px;
        }
        .portal-card {
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 36px 32px;
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .portal-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }
        .portal-icon {
            font-size: 40px;
            margin-bottom: 16px;
        }
        .portal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .portal-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 24px;
        }
    </style>
</head>
<body class="hero-section">
    <header class="hero-nav">
        <div class="hero-brand">
            <span>🏛️</span>
            <span>{{ config('app.name', 'Online Examination System') }}</span>
        </div>
        <div style="font-size: 13px; color: var(--text-muted);">
            University Examination & Assessment Management
        </div>
    </header>

    <main class="hero-content">
        <div class="portal-grid">
            <!-- Student Portal Card -->
            <div class="portal-card">
                <div>
                    <div class="portal-icon">🎓</div>
                    <h2 class="portal-title">Student Examination Portal</h2>
                    <p class="portal-desc">
                        Sign in to access examinations assigned to your academic batch, complete live multiple-choice assessments with an interactive countdown timer, save your answers, and view instant results.
                    </p>
                    <div style="background: #f8faf8; border: 1px solid var(--border-light); padding: 12px; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-muted); margin-bottom: 24px;">
                        <strong>Demo Students (Password: <code>password123</code>):</strong><br>
                        • <code>abir.banerjee@student.edu</code> (BTech CSE Sec A)<br>
                        • <code>bipasha.sarkar@student.edu</code> (BTech CSE Sec A)<br>
                        • <code>dinesh.kumar@student.edu</code> (BTech CSE Sec B)
                    </div>
                </div>

                <a href="{{ route('student.login') }}" class="btn btn-primary btn-lg" style="width: 100%;">
                    Access Student Portal →
                </a>
            </div>

            <!-- Administrator Portal Card -->
            <div class="portal-card">
                <div>
                    <div class="portal-icon">⚙️</div>
                    <h2 class="portal-title">Administrator Control Center</h2>
                    <p class="portal-desc">
                        Manage academic batches, student rosters, question banks, schedule examinations, publish tests, review live attempt metrics, and generate printable consolidated grade reports.
                    </p>
                    <div style="background: #f8faf8; border: 1px solid var(--border-light); padding: 12px; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-muted); margin-bottom: 24px;">
                        <strong>Administrator:</strong> Prof. M. Bose<br>
                        <strong>Admin ID:</strong> <code>01234</code> (5 digits) &bull; <strong>Password:</strong> <code>password123</code>
                    </div>
                </div>

                <a href="{{ route('admin.login') }}" class="btn btn-accent btn-lg" style="width: 100%;">
                    Access Admin Portal →
                </a>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 20px; font-size: 13px; color: var(--text-muted); border-top: 1px solid rgba(0,0,0,0.06); background: rgba(255,255,255,0.6);">
        {{ config('app.name') }} • Built with Laravel, Eloquent ORM, Blade, MySQL, and Vanilla JavaScript
    </footer>
</body>
</html>
