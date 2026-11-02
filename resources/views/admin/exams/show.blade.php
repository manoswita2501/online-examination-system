@extends('layouts.admin')

@section('title', $exam->title)
@section('page_title', 'Examination Overview')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline">← Back to Examinations</a>
    </div>

    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        @if ($exam->isDraft())
            <form action="{{ route('admin.exams.publish', $exam) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-accent btn-sm" onclick="return confirm('Publish this exam? It will become visible to batch students.');">
                    🚀 Publish Exam
                </button>
            </form>
        @elseif ($exam->isPublished())
            <form action="{{ route('admin.exams.close', $exam) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Close this exam? No new attempts will be allowed.');">
                    🔒 Close Exam
                </button>
            </form>
        @endif

        @if ($attemptsCount === 0)
            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary btn-sm">
                ✏️ Edit Exam
            </a>
        @endif

        <a href="{{ route('admin.reports.consolidated', $exam) }}" class="btn btn-primary btn-sm">
            📊 Consolidated Batch Report
        </a>
    </div>
</div>

<!-- Exam Summary Card -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div>
            <div class="card-title">{{ $exam->title }}</div>
            <div class="card-subtitle">{{ $exam->subject }}</div>
        </div>
        <div>
            <x-status-badge :status="$exam->status" />
        </div>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Assigned Batch</div>
                <div style="font-size: 15px; font-weight: 600;">
                    <a href="{{ route('admin.batches.show', $exam->batch) }}">
                        {{ $exam->batch->full_name }}
                    </a>
                </div>
            </div>

            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Scheduled Date & Time</div>
                <div style="font-size: 15px; font-weight: 600;">
                    {{ $exam->exam_date->format('d M Y') }} at {{ date('h:i A', strtotime($exam->start_time)) }}
                </div>
            </div>

            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Duration</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $exam->duration_minutes }} Minutes</div>
            </div>

            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Questions & Marks</div>
                <div style="font-size: 15px; font-weight: 600;">
                    {{ $exam->questions->count() }} Questions • {{ $exam->total_marks }} Max Marks ({{ $exam->marks_per_question }} mk/Q)
                </div>
            </div>
        </div>

        @if ($exam->instructions)
            <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border-light);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Instructions:</div>
                <div style="font-size: 13px; color: var(--text-secondary); white-space: pre-line;">{{ $exam->instructions }}</div>
            </div>
        @endif
    </div>
</div>

<div class="form-row">
    <!-- Questions List Section -->
    <div class="form-col" style="flex: 1.4;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Questions in this Exam ({{ $exam->questions->count() }})</div>
            </div>
            <div class="card-body">
                @forelse ($exam->questions as $i => $q)
                    <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 18px; margin-bottom: 18px;">
                        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                            <span class="badge badge-secondary" style="height: fit-content;">Q{{ $i + 1 }}</span>
                            <div style="font-weight: 600; font-size: 15px; color: var(--text-primary);">
                                {{ $q->question_text }}
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px;">
                            <div style="padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid {{ $q->correct_option === 'A' ? 'var(--accent)' : 'var(--border)' }}; background: {{ $q->correct_option === 'A' ? '#e8f5e9' : '#fafafa' }};">
                                <strong>(A)</strong> {{ $q->option_a }}
                                @if ($q->correct_option === 'A')
                                    <span style="color: var(--success); font-weight: 700; float: right;">✓ Correct</span>
                                @endif
                            </div>

                            <div style="padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid {{ $q->correct_option === 'B' ? 'var(--accent)' : 'var(--border)' }}; background: {{ $q->correct_option === 'B' ? '#e8f5e9' : '#fafafa' }};">
                                <strong>(B)</strong> {{ $q->option_b }}
                                @if ($q->correct_option === 'B')
                                    <span style="color: var(--success); font-weight: 700; float: right;">✓ Correct</span>
                                @endif
                            </div>

                            <div style="padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid {{ $q->correct_option === 'C' ? 'var(--accent)' : 'var(--border)' }}; background: {{ $q->correct_option === 'C' ? '#e8f5e9' : '#fafafa' }};">
                                <strong>(C)</strong> {{ $q->option_c }}
                                @if ($q->correct_option === 'C')
                                    <span style="color: var(--success); font-weight: 700; float: right;">✓ Correct</span>
                                @endif
                            </div>

                            <div style="padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid {{ $q->correct_option === 'D' ? 'var(--accent)' : 'var(--border)' }}; background: {{ $q->correct_option === 'D' ? '#e8f5e9' : '#fafafa' }};">
                                <strong>(D)</strong> {{ $q->option_d }}
                                @if ($q->correct_option === 'D')
                                    <span style="color: var(--success); font-weight: 700; float: right;">✓ Correct</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p>No questions added to this examination yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Student Attempts Summary -->
    <div class="form-col" style="flex: 1;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Student Submissions ({{ $submittedCount }})</div>
                <a href="{{ route('admin.reports.consolidated', $exam) }}" class="btn btn-sm btn-outline">Full Report</a>
            </div>
            <div class="card-body" style="padding: 0;">
                @if ($exam->attempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exam->attempts as $attempt)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;">{{ $attempt->student->name }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ $attempt->student->student_id }}</div>
                                        </td>
                                        <td>
                                            <strong>{{ $attempt->score }} / {{ $attempt->max_score }}</strong>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ $attempt->percentage }}%</div>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$attempt->status" />
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('admin.reports.student', $attempt) }}" class="btn btn-sm btn-outline">Report</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>No students have taken this examination yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
