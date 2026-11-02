@extends('layouts.admin')

@section('title', 'Edit Batch')
@section('page_title', 'Edit Academic Batch')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-sm btn-outline">
            ← Back to Batch Details
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Edit Batch Details</div>
        </div>

        <form action="{{ route('admin.batches.update', $batch) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="form-group">
                    <label for="course_name" class="form-label">Course / Degree Program <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="course_name" 
                        id="course_name" 
                        class="form-control @error('course_name') is-invalid @enderror" 
                        value="{{ old('course_name', $batch->course_name) }}" 
                        required
                    >
                    @error('course_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">Department / School <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="department" 
                        id="department" 
                        class="form-control @error('department') is-invalid @enderror" 
                        value="{{ old('department', $batch->department) }}" 
                        required
                    >
                    @error('department')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="semester" class="form-label">Semester <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="semester" 
                                id="semester" 
                                class="form-control @error('semester') is-invalid @enderror" 
                                value="{{ old('semester', $batch->semester) }}" 
                                required
                            >
                            @error('semester')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group">
                            <label for="section" class="form-label">Section / Group <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="section" 
                                id="section" 
                                class="form-control @error('section') is-invalid @enderror" 
                                value="{{ old('section', $batch->section) }}" 
                                required
                            >
                            @error('section')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="academic_year" class="form-label">Academic Year <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="academic_year" 
                        id="academic_year" 
                        class="form-control @error('academic_year') is-invalid @enderror" 
                        value="{{ old('academic_year', $batch->academic_year) }}" 
                        required
                    >
                    @error('academic_year')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Batch</button>
            </div>
        </form>
    </div>
</div>
@endsection
