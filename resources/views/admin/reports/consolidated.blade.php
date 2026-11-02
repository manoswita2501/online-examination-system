@extends('layouts.admin')

@section('title', 'Consolidated Batch Report: ' . $exam->title)
@section('page_title', 'Consolidated Examination Report')

@section('content')
<div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline">← Back to Exam Overview</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary" style="margin-left: 8px;">Results Directory</a>
    </div>

    <div>
        <button type="button" onclick="window.print();" class="btn btn-primary">
            <span>🖨️ Print Batch Report / Save as PDF</span>
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: #ffffff; padding: 22px 28px;">
        <div>
            <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); margin-bottom: 6px;">
                Official Consolidated Batch Result
            </span>
            <h1 style="font-size: 22px; font-weight: 800; color: #ffffff; margin-bottom: 4px;">{{ $exam->title }}</h1>
            <p style="font-size: 13px; color: #d1fae5;">
                {{ $exam->subject }} • Batch: <strong>{{ $batch->full_name }}</strong>
            </p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">Scheduled Date</div>
            <div style="font-size: 16px; font-weight: 700;">{{ $exam->exam_date->format('d M Y') }}</div>
            <div style="font-size: 12px; opacity: 0.9;">{{ $exam->duration_minutes }} Mins • {{ $exam->questions->count() }} MCQs ({{ $exam->total_marks }} Max Marks)</div>
        </div>
    </div>

    <!-- Consolidated Statistical Analytics Strip -->
    <div class="result-stats-row">
        <div class="result-stat-box">
            <div class="result-stat-num">{{ $assignedCount }}</div>
            <div class="result-stat-title">Assigned Students</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--primary);">{{ $completedCount }}</div>
            <div class="result-stat-title">Completed ({{ $submittedAttempts }} Normal / {{ $autoSubmittedAttempts }} Auto)</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--text-muted);">{{ $unattemptedCount }}</div>
            <div class="result-stat-title">Not Attempted</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--accent);">
                {{ $completedCount > 0 ? $averageScore : '—' }}
            </div>
            <div class="result-stat-title">Average Score</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--success);">
                {{ $completedCount > 0 ? $highestScore : '—' }}
            </div>
            <div class="result-stat-title">Highest Score</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--danger);">
                {{ $completedCount > 0 ? $lowestScore : '—' }}
            </div>
            <div class="result-stat-title">Lowest Score</div>
        </div>
    </div>

    <!-- Student Cohort Roster Table -->
    <div class="card-body" style="padding: 0;">
        @if ($assignedStudents->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Student ID</th>
                            <th>Student Full Name</th>
                            <th>Email</th>
                            <th>Submission Mode</th>
                            <th>Correct</th>
                            <th>Incorrect</th>
                            <th>Unanswered</th>
                            <th>Marks Obtained</th>
                            <th>Percentage</th>
                            <th style="text-align: right;" class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignedStudents as $index => $student)
                            @php
                                $attempt = $attempts->get($student->id);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $student->student_id }}</code></td>
                                <td>
                                    <strong>{{ $student->name }}</strong>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @if ($attempt && $attempt->isSubmitted())
                                        <x-status-badge :status="$attempt->status" />
                                    @elseif ($attempt && $attempt->isInProgress())
                                        <span class="badge badge-warning">In Progress</span>
                                    @else
                                        <span class="badge badge-secondary">Did Not Take</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $attempt && $attempt->isSubmitted() ? $attempt->correct_count : '—' }}
                                </td>
                                <td>
                                    {{ $attempt && $attempt->isSubmitted() ? $attempt->incorrect_count : '—' }}
                                </td>
                                <td>
                                    {{ $attempt && $attempt->isSubmitted() ? $attempt->unanswered_count : '—' }}
                                </td>
                                <td>
                                    @if ($attempt && $attempt->isSubmitted())
                                        <strong style="color: var(--primary);">
                                            {{ $attempt->score }} / {{ $exam->total_marks }}
                                        </strong>
                                    @else
                                        <span style="color: var(--text-muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($attempt && $attempt->isSubmitted())
                                        <span style="font-weight: 700; color: {{ $attempt->percentage >= 40 ? 'var(--success)' : 'var(--danger)' }};">
                                            {{ $attempt->percentage }}%
                                        </span>
                                    @else
                                        <span style="color: var(--text-muted);">—</span>
                                    @endif
                                </td>
                                <td style="text-align: right;" class="no-print">
                                    @if ($attempt && $attempt->isSubmitted())
                                        <a href="{{ route('admin.reports.student', $attempt) }}" class="btn btn-sm btn-outline">
                                            View Report
                                        </a>
                                    @else
                                        <span style="font-size: 12px; color: var(--text-muted);">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>No students enrolled in this batch.</p>
            </div>
        @endif
    </div>

    <!-- Print Footer Signature Area -->
    <div class="print-only-footer">
        <div style="text-align: center; width: 220px;">
            <div style="height: 50px;"></div>
            <div style="border-top: 1px solid #000; padding-top: 4px; font-size: 11pt;">Head of Department</div>
        </div>
        <div style="text-align: center; width: 250px;">
            <div style="height: 50px;"></div>
            <div style="border-top: 1px solid #000; padding-top: 4px; font-size: 11pt;">Controller of Examinations</div>
        </div>
    </div>
</div>
@endsection
