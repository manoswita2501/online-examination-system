@extends('layouts.admin')

@section('title', 'Add New Batch')
@section('page_title', 'Create Academic Batch')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.batches.index') }}" class="btn btn-sm btn-outline">
            ← Back to Batches
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Batch Information</div>
        </div>

        <form action="{{ route('admin.batches.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="form-group">
                    <label for="course_name" class="form-label">Course / Degree Program <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="course_name" 
                        id="course_name" 
                        class="form-control @error('course_name') is-invalid @enderror" 
                        value="{{ old('course_name') }}" 
                        placeholder="e.g., BTech CSE, MCA, BCA" 
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
                        value="{{ old('department') }}" 
                        placeholder="e.g., Computer Science & Engineering" 
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
                                value="{{ old('semester') }}" 
                                placeholder="e.g., Semester 1, Semester 4" 
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
                                value="{{ old('section') }}" 
                                placeholder="e.g., Section A, Section B" 
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
                        value="{{ old('academic_year', '2025-2026') }}" 
                        placeholder="e.g., 2025-2026" 
                        required
                    >
                    @error('academic_year')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Academic Batch</button>
            </div>
        </form>
    </div>
</div>
@endsection
