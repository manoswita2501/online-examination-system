@extends('layouts.admin')

@section('title', 'Edit Student: ' . $student->name)
@section('page_title', 'Edit Student Account')

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline">
            ← Back to Student Profile
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Edit Student Credentials & Batch</div>
        </div>

        <form action="{{ route('admin.students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

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
                                value="{{ old('name', $student->name) }}" 
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
                                value="{{ old('student_id', $student->student_id) }}" 
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
                                value="{{ old('email', $student->email) }}" 
                                required
                            >
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group">
                            <label for="password" class="form-label">Reset Password (Leave blank to keep current)</label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Min. 8 characters"
                            >
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="batch_id" class="form-label">Assign Academic Batch <span class="required">*</span></label>
                    <select name="batch_id" id="batch_id" class="form-control @error('batch_id') is-invalid @enderror" required>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" {{ (old('batch_id', $student->batch_id) == $batch->id) ? 'selected' : '' }}>
                                {{ $batch->course_name }} - {{ $batch->semester }} ({{ $batch->section }}, {{ $batch->academic_year }})
                            </option>
                        @endforeach
                    </select>
                    @error('batch_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $student->is_active) ? 'checked' : '' }} style="accent-color: var(--primary);">
                        <span>Account is active</span>
                    </label>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>
    </div>
</div>
@endsection
