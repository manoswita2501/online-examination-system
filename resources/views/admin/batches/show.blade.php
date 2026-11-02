@extends('layouts.admin')

@section('title', $batch->full_name)
@section('page_title', 'Batch Details')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <a href="{{ route('admin.batches.index') }}" class="btn btn-sm btn-outline">← Back to All Batches</a>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-secondary btn-sm">Edit Batch</a>
        <a href="{{ route('admin.students.create', ['batch_id' => $batch->id]) }}" class="btn btn-primary btn-sm">➕ Add Student to Batch</a>
        <a href="{{ route('admin.exams.create', ['batch_id' => $batch->id]) }}" class="btn btn-accent btn-sm">📝 Create Exam for Batch</a>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title">{{ $batch->course_name }}</div>
        <span class="badge badge-info">{{ $batch->academic_year }}</span>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Department</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $batch->department }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Semester & Section</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $batch->semester }} • {{ $batch->section }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total Enrolled</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $batch->students->count() }} Students</div>
            </div>
            <div class="form-col">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Scheduled Exams</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $batch->exams->count() }} Examinations</div>
            </div>
        </div>
    </div>
</div>

<div class="form-row">
    <!-- Students in this batch -->
    <div class="form-col">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Enrolled Students ({{ $batch->students->count() }})</div>
            </div>
            <div class="card-body" style="padding: 0;">
                @if ($batch->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($batch->students as $student)
                                    <tr>
                                        <td><code>{{ $student->student_id }}</code></td>
                                        <td>
                                            <a href="{{ route('admin.students.show', $student) }}" style="font-weight: 600;">
                                                {{ $student->name }}
                                            </a>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ $student->email }}</div>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$student->is_active ? 'active' : 'inactive'" />
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>No students enrolled in this batch yet.</p>
                        <a href="{{ route('admin.students.create', ['batch_id' => $batch->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 8px;">Enroll Student</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Examinations for this batch -->
    <div class="form-col">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Batch Examinations ({{ $batch->exams->count() }})</div>
            </div>
            <div class="card-body" style="padding: 0;">
                @if ($batch->exams->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Exam Title</th>
                                    <th>Schedule</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($batch->exams as $exam)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.exams.show', $exam) }}" style="font-weight: 600;">
                                                {{ $exam->title }}
                                            </a>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ $exam->questions_count }} MCQs • {{ $exam->duration_minutes }} min</div>
                                        </td>
                                        <td>
                                            <div>{{ $exam->exam_date->format('d M Y') }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ date('h:i A', strtotime($exam->start_time)) }}</div>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$exam->status" />
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>No examinations scheduled for this batch yet.</p>
                        <a href="{{ route('admin.exams.create', ['batch_id' => $batch->id]) }}" class="btn btn-accent btn-sm" style="margin-top: 8px;">Create Exam</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
