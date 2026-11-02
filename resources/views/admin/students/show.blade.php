@extends('layouts.admin')

@section('title', 'Student Profile: ' . $student->name)
@section('page_title', 'Student Details')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline">← Back to Student Directory</a>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-secondary btn-sm">Edit Student</a>
        <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm {{ $student->is_active ? 'btn-outline-danger' : 'btn-accent' }}">
                {{ $student->is_active ? 'Deactivate Account' : 'Reactivate Account' }}
            </button>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title">{{ $student->name }}</div>
        <div>
            <x-status-badge :status="$student->is_active ? 'active' : 'inactive'" />
        </div>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Student ID / Roll No</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--primary);">{{ $student->student_id }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Email Address</div>
                <div style="font-size: 15px; font-weight: 500;">{{ $student->email }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Assigned Batch</div>
                <div style="font-size: 15px; font-weight: 600;">
                    @if ($student->batch)
                        <a href="{{ route('admin.batches.show', $student->batch) }}">
                            {{ $student->batch->full_name }}
                        </a>
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </div>
            </div>
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Department</div>
                <div style="font-size: 15px; font-weight: 500;">{{ $student->batch->department ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Examination History Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Examination Attempts History ({{ $student->attempts->count() }})</div>
    </div>
    <div class="card-body" style="padding: 0;">
        @if ($student->attempts->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Examination</th>
                            <th>Subject</th>
                            <th>Started At</th>
                            <th>Submitted At</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($student->attempts as $attempt)
                            <tr>
                                <td>
                                    <strong style="color: var(--primary);">{{ $attempt->exam->title }}</strong>
                                </td>
                                <td>{{ $attempt->exam->subject }}</td>
                                <td>{{ $attempt->started_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, h:i A') : '—' }}</td>
                                <td>
                                    <strong>{{ $attempt->score }} / {{ $attempt->max_score }}</strong>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $attempt->percentage }}%</span>
                                </td>
                                <td>
                                    <x-status-badge :status="$attempt->status" />
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.reports.student', $attempt) }}" class="btn btn-sm btn-outline">
                                        View Result
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>This student has not participated in any examinations yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
