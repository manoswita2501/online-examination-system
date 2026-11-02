@extends('layouts.auth')

@section('title', 'Administrator Login')

@section('content')
<h2 style="font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 6px;">Administrator Sign In</h2>
<p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Enter your 5-digit Administrator ID and password to access the control panel.</p>

<form action="{{ route('admin.login.submit') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="admin_id" class="form-label">5-Digit Admin ID <span class="required">*</span></label>
        <input 
            type="text" 
            name="admin_id" 
            id="admin_id" 
            class="form-control @error('admin_id') is-invalid @enderror" 
            value="{{ old('admin_id') }}" 
            placeholder="e.g., 01234" 
            maxlength="5"
            pattern="[0-9]{5}"
            title="5 digits numeric Administrator ID"
            required 
            autofocus
        >
        <div class="form-text">Must be exactly 5 numeric digits (leading zeros preserved).</div>
        @error('admin_id')
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
        Sign In to Control Center
    </button>
</form>

<div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-light); font-size: 12px; color: var(--text-muted); background: #f8faf8; padding: 12px; border-radius: var(--radius-sm);">
    <strong>Default Administrator Credentials:</strong><br>
    • Administrator: <strong>Prof. M. Bose</strong><br>
    • Admin ID: <code>01234</code><br>
    • Password: <code>password123</code><br>
    <em>Restricted administrative access. Public registration is disabled.</em>
</div>
@endsection
