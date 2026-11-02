@props(['status'])

@php
    $statusClass = match(strtolower($status)) {
        'published' => 'badge-success',
        'draft' => 'badge-warning',
        'closed' => 'badge-danger',
        'in_progress' => 'badge-info',
        'submitted' => 'badge-success',
        'auto_submitted' => 'badge-warning',
        'active' => 'badge-success',
        'inactive' => 'badge-danger',
        default => 'badge-secondary',
    };

    $statusLabel = match(strtolower($status)) {
        'in_progress' => 'In Progress',
        'auto_submitted' => 'Auto Submitted',
        default => ucfirst($status),
    };
@endphp

<span class="badge {{ $statusClass }}">
    {{ $statusLabel }}
</span>
