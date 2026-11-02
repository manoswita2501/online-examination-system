@extends('layouts.admin')

@section('title', 'Students Directory')
@section('page_title', 'Student Management')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--primary);">Enrolled Students</h2>
        <p style="font-size: 13px; color: var(--text-muted);">Manage student credentials, batch assignments, and account statuses.</p>
    </div>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <span>➕ Enroll New Student</span>
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('admin.students.index') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Search Student</label>
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by name, email, or student ID..." 
                    value="{{ request('search') }}"
                >
            </div>

            <div style="flex: 1.5; min-width: 200px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Filter by Batch</label>
                <select name="batch_id" class="form-control">
                    <option value="">-- All Batches --</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->course_name }} - {{ $batch->semester }} ({{ $batch->section }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Search</button>
                @if (request()->hasAny(['search', 'batch_id']))
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        @if ($students->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Assigned Batch</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>
                                    <code>{{ $student->student_id }}</code>
                                </td>
                                <td>
                                    <a href="{{ route('admin.students.show', $student) }}" style="font-weight: 600;">
                                        {{ $student->name }}
                                    </a>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @if ($student->batch)
                                        <a href="{{ route('admin.batches.show', $student->batch) }}">
                                            {{ $student->batch->course_name }} ({{ $student->batch->section }})
                                        </a>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :status="$student->is_active ? 'active' : 'inactive'" />
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline">
                                            View
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-secondary">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="btn btn-sm {{ $student->is_active ? 'btn-outline-danger' : 'btn-accent' }}" 
                                                onclick="return confirm('Change status for {{ $student->name }}?');"
                                            >
                                                {{ $student->is_active ? 'Deactivate' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $students->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">👨‍🎓</div>
                <p>No students found matching your criteria.</p>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm" style="margin-top: 8px;">Enroll New Student</a>
            </div>
        @endif
    </div>
</div>
@endsection
