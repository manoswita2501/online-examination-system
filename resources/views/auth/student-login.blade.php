@extends('layouts.auth')

@section('title', 'Student Login')

@section('content')
<h2 style="font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 6px;">Student Sign In</h2>
<p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Enter your student email and password to access your examinations.</p>

<form action="{{ route('student.login.submit') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">Registered Student Email <span class="required">*</span></label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            class="form-control @error('email') is-invalid @enderror" 
            value="{{ old('email') }}" 
            placeholder="e.g., john.doe@student.edu" 
            required 
            autofocus
        >
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password <span class="required">*</span></label>
        <input 
            type="password" 
            name="password" 
            id="password" 
            class="form-control @error('password') is-invalid @enderror" 
            placeholder="••••••••" 
            required
        >
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); cursor: pointer;">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="accent-color: var(--primary);">
            Remember this session
        </label>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px;">
        Sign In to Student Portal
    </button>
</form>

<div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-light); font-size: 12px; color: var(--text-muted); background: #f8faf8; padding: 12px; border-radius: var(--radius-sm);">
    <strong>Demo Student Accounts:</strong><br>
    • <code>abir.banerjee@student.edu</code> / <code>password123</code> (BTech CSE Sec A)<br>
    • <code>bipasha.sarkar@student.edu</code> / <code>password123</code> (BTech CSE Sec A)<br>
    • <code>dinesh.kumar@student.edu</code> / <code>password123</code> (BTech CSE Sec B)<br>
    <em>Account creation is managed by the administrator.</em>
</div>
@endsection
