@extends('layouts.admin')

@section('title', 'Examination Results & Reports')
@section('page_title', 'Results & Academic Reports')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--primary);">Candidate Results Directory</h2>
        <p style="font-size: 13px; color: var(--text-muted);">View individual performance records, batch summaries, and printable score sheets.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card no-print" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1.5; min-width: 200px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Examination</label>
                <select name="exam_id" class="form-control">
                    <option value="">-- All Examinations --</option>
                    @foreach ($exams as $e)
                        <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->title }} ({{ $e->batch->course_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1.5; min-width: 200px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Academic Batch</label>
                <select name="batch_id" class="form-control">
                    <option value="">-- All Batches --</option>
                    @foreach ($batches as $b)
                        <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->course_name }} - {{ $b->semester }} ({{ $b->section }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1.5; min-width: 180px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Student Search</label>
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Name, email, student ID..." 
                    value="{{ request('search') }}"
                >
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Filter</button>
                @if (request()->hasAny(['exam_id', 'batch_id', 'search']))
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        @if ($attempts->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Batch</th>
                            <th>Examination</th>
                            <th>Correct</th>
                            <th>Incorrect</th>
                            <th>Unanswered</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th style="text-align: right;" class="no-print">Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attempts as $attempt)
                            <tr>
                                <td><code>{{ $attempt->student->student_id }}</code></td>
                                <td>
                                    <a href="{{ route('admin.students.show', $attempt->student) }}" style="font-weight: 600;">
                                        {{ $attempt->student->name }}
                                    </a>
                                </td>
                                <td>{{ $attempt->student->batch->course_name }} ({{ $attempt->student->batch->section }})</td>
                                <td>
                                    <a href="{{ route('admin.reports.consolidated', $attempt->exam) }}">
                                        {{ Str::limit($attempt->exam->title, 24) }}
                                    </a>
                                </td>
                                <td><span style="color: var(--success); font-weight: 600;">{{ $attempt->correct_count }}</span></td>
                                <td><span style="color: var(--danger); font-weight: 600;">{{ $attempt->incorrect_count }}</span></td>
                                <td><span style="color: var(--text-muted);">{{ $attempt->unanswered_count }}</span></td>
                                <td>
                                    <strong style="color: var(--primary);">{{ $attempt->score }} / {{ $attempt->max_score }}</strong>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: {{ $attempt->percentage >= 40 ? 'var(--success)' : 'var(--danger)' }};">
                                        {{ $attempt->percentage }}%
                                    </span>
                                </td>
                                <td>
                                    <x-status-badge :status="$attempt->status" />
                                </td>
                                <td>
                                    <div style="font-size: 12px;">{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y') : '—' }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $attempt->submitted_at ? $attempt->submitted_at->format('h:i A') : '' }}</div>
                                </td>
                                <td style="text-align: right;" class="no-print">
                                    <a href="{{ route('admin.reports.student', $attempt) }}" class="btn btn-sm btn-outline">
                                        View Score Sheet
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="no-print" style="padding: 16px 20px;">
                {{ $attempts->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📊</div>
                <p>No completed examination results found matching the filter criteria.</p>
            </div>
        @endif
    </div>
</div>
@endsection
