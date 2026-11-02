@extends('layouts.admin')

@section('title', 'Examinations')
@section('page_title', 'Examinations Directory')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--primary);">Scheduled Examinations</h2>
        <p style="font-size: 13px; color: var(--text-muted);">Manage scheduled assessments, questions, publishing status, and student attempts.</p>
    </div>
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
        <span>➕ Create Examination</span>
    </a>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('admin.exams.index') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Target Batch</label>
                <select name="batch_id" class="form-control">
                    <option value="">-- All Batches --</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->course_name }} - {{ $batch->semester }} ({{ $batch->section }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1.5; min-width: 160px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Publication Status</label>
                <select name="status" class="form-control">
                    <option value="">-- All Statuses --</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Filter</button>
                @if (request()->hasAny(['batch_id', 'status']))
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if ($exams->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exam Title & Subject</th>
                            <th>Target Batch</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Questions</th>
                            <th>Attempts</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exams as $exam)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.exams.show', $exam) }}" style="font-weight: 600;">
                                        {{ $exam->title }}
                                    </a>
                                    <div style="font-size: 12px; color: var(--text-muted);">{{ $exam->subject }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.batches.show', $exam->batch) }}">
                                        {{ $exam->batch->course_name }} ({{ $exam->batch->section }})
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $exam->exam_date->format('d M Y') }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ date('h:i A', strtotime($exam->start_time)) }}</div>
                                </td>
                                <td>{{ $exam->duration_minutes }} mins</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $exam->questions_count }} MCQs</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.reports.consolidated', $exam) }}">
                                        <span class="badge badge-info">{{ $exam->attempts_count }} Taken</span>
                                    </a>
                                </td>
                                <td>
                                    <x-status-badge :status="$exam->status" />
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline">
                                            View
                                        </a>

                                        @if ($exam->isDraft() || $exam->isClosed())
                                            <form action="{{ route('admin.exams.publish', $exam) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-accent" onclick="return confirm('{{ $exam->isClosed() ? 'Re-open' : 'Publish' }} this exam? Students will be able to take it.');">
                                                    {{ $exam->isClosed() ? 'Re-Open' : 'Publish' }}
                                                </button>
                                            </form>
                                        @elseif ($exam->isPublished())
                                            <form action="{{ route('admin.exams.close', $exam) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Close this exam? Students will no longer be able to start it.');">
                                                    Close
                                                </button>
                                            </form>
                                        @endif

                                        @if ($exam->attempts_count === 0)
                                            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-secondary">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this exam?');" style="display: inline;">
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

            <div style="padding: 16px 20px;">
                {{ $exams->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <p>No examinations found matching your criteria.</p>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm" style="margin-top: 8px;">Create New Exam</a>
            </div>
        @endif
    </div>
</div>
@endsection
