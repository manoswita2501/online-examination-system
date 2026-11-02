@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Academic Control Dashboard')

@section('content')
<!-- Metric Cards -->
<div class="metrics-grid">
    <div class="stat-card">
        <div class="stat-label">Total Students</div>
        <div class="stat-value">{{ $totalStudents }}</div>
        <div class="stat-helper">Enrolled across batches</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Academic Batches</div>
        <div class="stat-value">{{ $totalBatches }}</div>
        <div class="stat-helper">Departments & semesters</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Exams</div>
        <div class="stat-value">{{ $totalExams }}</div>
        <div class="stat-helper">{{ $publishedExams }} Published • {{ $draftExams }} Draft</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Completed Attempts</div>
        <div class="stat-value">{{ $completedAttemptsCount }}</div>
        <div class="stat-helper">Graded submissions</div>
    </div>
</div>

<!-- Quick Action Strip -->
<div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
        <span>➕ Create Examination</span>
    </a>
    <a href="{{ route('admin.students.create') }}" class="btn btn-secondary">
        <span>👨‍🎓 Enroll Student</span>
    </a>
    <a href="{{ route('admin.batches.create') }}" class="btn btn-secondary">
        <span>📚 Add Batch</span>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline" style="margin-left: auto;">
        <span>📊 View All Results</span>
    </a>
</div>

<div class="form-row">
    <!-- Upcoming Exams -->
    <div class="form-col" style="flex: 1.2;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span>📅</span>
                    <span>Upcoming & Active Examinations</span>
                </div>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline">View All</a>
            </div>

            <div class="card-body" style="padding: 0;">
                @if ($upcomingExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Exam Title</th>
                                    <th>Target Batch</th>
                                    <th>Schedule</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingExams as $exam)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.exams.show', $exam) }}" style="font-weight: 600;">
                                                {{ $exam->title }}
                                            </a>
                                            <div style="font-size: 12px; color: var(--text-muted);">{{ $exam->subject }}</div>
                                        </td>
                                        <td>{{ $exam->batch->course_name }} ({{ $exam->batch->section }})</td>
                                        <td>
                                            <div>{{ $exam->exam_date->format('d M Y') }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ date('h:i A', strtotime($exam->start_time)) }}</div>
                                        </td>
                                        <td>{{ $exam->duration_minutes }} min</td>
                                        <td>
                                            <x-status-badge :status="$exam->status" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📝</div>
                        <p>No upcoming examinations scheduled.</p>
                        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm" style="margin-top: 10px;">Schedule One Now</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Student Submissions -->
    <div class="form-col" style="flex: 1;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span>🏆</span>
                    <span>Recent Exam Submissions</span>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline">Full Reports</a>
            </div>

            <div class="card-body" style="padding: 0;">
                @if ($recentAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Exam</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;">{{ $attempt->student->name }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">ID: {{ $attempt->student->student_id }}</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px;">{{ Str::limit($attempt->exam->title, 24) }}</div>
                                        </td>
                                        <td>
                                            <strong style="color: var(--primary);">{{ $attempt->score }} / {{ $attempt->max_score }}</strong>
                                            <span style="font-size: 11px; color: var(--text-muted);">({{ $attempt->percentage }}%)</span>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$attempt->status" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📈</div>
                        <p>No examination attempts submitted yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
