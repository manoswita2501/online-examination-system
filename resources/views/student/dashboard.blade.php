@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<!-- Student Profile Welcome Banner -->
<div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--primary);">
    <div class="card-body" style="padding: 22px 28px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 22px; font-weight: 700; color: var(--primary); margin-bottom: 4px;">
                    Welcome, {{ $student->name }}
                </h1>
                <p style="font-size: 14px; color: var(--text-muted);">
                    Student ID: <strong>{{ $student->student_id }}</strong> • Email: {{ $student->email }}
                </p>
            </div>

            @if ($batch)
                <div style="text-align: right; background: var(--accent-light); border: 1px solid var(--accent-border); padding: 10px 18px; border-radius: var(--radius-md);">
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--primary); letter-spacing: 0.5px;">Enrolled Academic Batch</div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--primary);">{{ $batch->course_name }} ({{ $batch->section }})</div>
                    <div style="font-size: 12px; color: var(--text-secondary);">{{ $batch->semester }} • {{ $batch->academic_year }}</div>
                </div>
            @else
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    Your account is not assigned to any academic batch. Please contact the administrator.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Active In-Progress Sessions (if any) -->
@if ($inProgressAttempts->count() > 0)
    <div class="card" style="border: 2px solid var(--warning); margin-bottom: 24px; background: #fffdf5;">
        <div class="card-header" style="background: var(--warning-bg); border-bottom-color: #fde68a;">
            <div class="card-title" style="color: var(--warning);">
                <span>⚠️</span>
                <span>Active Examination in Progress</span>
            </div>
        </div>
        <div class="card-body">
            @foreach ($inProgressAttempts as $inProg)
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h3 style="font-size: 17px; font-weight: 700; color: var(--primary);">{{ $inProg->exam->title }}</h3>
                        <p style="font-size: 13px; color: var(--text-muted);">
                            {{ $inProg->exam->subject }} • Started at: {{ $inProg->started_at->format('h:i A') }}
                        </p>
                    </div>
                    <a href="{{ route('student.attempts.show', $inProg) }}" class="btn btn-primary btn-lg">
                        <span>▶ Resume Examination Now</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Currently Available Examinations -->
<div class="card" style="margin-bottom: 26px;">
    <div class="card-header">
        <div class="card-title">
            <span>📝</span>
            <span>Available Examinations to Take</span>
        </div>
        <span class="badge badge-success">{{ $availableExams->count() }} Available</span>
    </div>
    <div class="card-body" style="padding: 0;">
        @if ($availableExams->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Examination</th>
                            <th>Subject & Code</th>
                            <th>Target Course</th>
                            <th>Duration</th>
                            <th>Total Questions</th>
                            <th>Max Marks</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($availableExams as $exam)
                            <tr>
                                <td>
                                    <strong style="color: var(--primary); font-size: 15px;">{{ $exam->title }}</strong>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                        Scheduled: {{ $exam->exam_date->format('d M Y') }} • {{ date('h:i A', strtotime($exam->start_time)) }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--text-main);">{{ $exam->subject }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info" style="font-size: 11px;">
                                        {{ $exam->batch ? $exam->batch->course_name . ' (' . $exam->batch->section . ')' : 'All BTech / BCA' }}
                                    </span>
                                </td>
                                <td>{{ $exam->duration_minutes }} Minutes</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $exam->questions_count }} MCQs</span>
                                </td>
                                <td>
                                    <strong>{{ $exam->total_marks }} Marks</strong>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('student.exams.instructions', $exam) }}" class="btn btn-primary btn-sm">
                                        View Instructions & Start →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">✅</div>
                <p>No new examinations are currently open.</p>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Check back according to your academic schedule.</div>
            </div>
        @endif
    </div>
</div>

<div class="form-row">
    <!-- Upcoming Exams -->
    <div class="form-col">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span>📅</span>
                    <span>Upcoming Examinations</span>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                @if ($upcomingExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Exam Title</th>
                                    <th>Scheduled For</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingExams as $exam)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;">{{ $exam->title }}</div>
                                            <div style="font-size: 12px; color: var(--text-muted);">{{ $exam->subject }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $exam->exam_date->format('d M Y') }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ date('h:i A', strtotime($exam->start_time)) }}</div>
                                        </td>
                                        <td>{{ $exam->duration_minutes }} min</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>No upcoming exams scheduled for your batch at this time.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Completed Examinations & Results -->
    <div class="form-col">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span>🏆</span>
                    <span>Completed Results & Grade Sheets</span>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                @if ($completedAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Examination</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($completedAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;">{{ $attempt->exam->title }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">Submitted: {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, h:i A') : 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <strong style="color: var(--primary);">{{ $attempt->score }} / {{ $attempt->max_score }}</strong>
                                        </td>
                                        <td>
                                            <span style="font-weight: 600; color: {{ $attempt->percentage >= 40 ? 'var(--success)' : 'var(--danger)' }};">
                                                {{ $attempt->percentage }}%
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: inline-flex; gap: 6px;">
                                                <a href="{{ route('student.results.show', $attempt) }}" class="btn btn-sm btn-outline">
                                                    View Result
                                                </a>
                                                <form action="{{ route('student.exams.start', $attempt->exam) }}" method="POST" style="display: inline;" onsubmit="return confirm('Retake this examination for practice? Previous attempt will be reset.');">
                                                    @csrf
                                                    <input type="hidden" name="retake" value="1">
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Retake this exam in real time">
                                                        Retake
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>You have not completed any examinations yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
