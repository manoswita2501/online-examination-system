@extends('layouts.admin')

@section('title', 'Enroll New Student')
@section('page_title', 'Student Enrollment')

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline">
            ← Back to Student Directory
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Enroll Student Account</div>
            <div class="card-subtitle">Administrators generate all student credentials; public registration is disabled.</div>
        </div>

        <form action="{{ route('admin.students.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="name" class="form-label">Student Full Name <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" 
                                placeholder="e.g., John Doe" 
                                required
                            >
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group">
                            <label for="student_id" class="form-label">Student ID / Roll No <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="student_id" 
                                id="student_id" 
                                class="form-control @error('student_id') is-invalid @enderror" 
                                value="{{ old('student_id') }}" 
                                placeholder="e.g., STU2025001" 
                                required
                            >
                            @error('student_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="email" class="form-label">Student Email Address <span class="required">*</span></label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="e.g., student@university.edu" 
                                required
                            >
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group">
                            <label for="password" class="form-label">Initial Password <span class="required">*</span></label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Min. 8 characters" 
                                required
                            >
                            <div class="form-text">Must be at least 8 characters. Will be securely hashed.</div>
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="batch_id" class="form-label">Assign Academic Batch <span class="required">*</span></label>
                    <select name="batch_id" id="batch_id" class="form-control @error('batch_id') is-invalid @enderror" required>
                        <option value="">-- Select Target Batch --</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" {{ (old('batch_id', $selectedBatchId) == $batch->id) ? 'selected' : '' }}>
                                {{ $batch->course_name }} - {{ $batch->semester }} ({{ $batch->section }}, {{ $batch->academic_year }}) - {{ $batch->department }}
                            </option>
                        @endforeach
                    </select>
                    @error('batch_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="accent-color: var(--primary);">
                        <span>Account is active (allows student to sign in and take scheduled exams)</span>
                    </label>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Enroll Student</button>
            </div>
        </form>
    </div>
</div>
@endsection
