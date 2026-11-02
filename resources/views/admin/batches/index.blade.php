@extends('layouts.admin')

@section('title', 'Academic Batches')
@section('page_title', 'Academic Batches')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--primary);">Academic Batches Directory</h2>
        <p style="font-size: 13px; color: var(--text-muted);">Manage academic courses, departments, semesters, and class sections.</p>
    </div>
    <a href="{{ route('admin.batches.create') }}" class="btn btn-primary">
        <span>➕ Add New Batch</span>
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if ($batches->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Batch / Course</th>
                            <th>Department</th>
                            <th>Semester & Section</th>
                            <th>Academic Year</th>
                            <th>Enrolled Students</th>
                            <th>Examinations</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.batches.show', $batch) }}" style="font-weight: 600;">
                                        {{ $batch->course_name }}
                                    </a>
                                </td>
                                <td>{{ $batch->department }}</td>
                                <td>
                                    <strong>{{ $batch->semester }}</strong> • {{ $batch->section }}
                                </td>
                                <td>{{ $batch->academic_year }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $batch->students_count }} Students</span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $batch->exams_count }} Exams</span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-sm btn-outline" title="View details">
                                            View
                                        </a>
                                        <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-sm btn-secondary" title="Edit batch">
                                            Edit
                                        </a>
                                        @if ($batch->students_count == 0 && $batch->exams_count == 0)
                                            <form action="{{ route('admin.batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this batch?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <p>No academic batches created yet.</p>
                <a href="{{ route('admin.batches.create') }}" class="btn btn-primary btn-sm" style="margin-top: 10px;">Create First Batch</a>
            </div>
        @endif
    </div>
</div>
@endsection
