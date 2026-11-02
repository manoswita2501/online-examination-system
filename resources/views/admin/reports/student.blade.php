@extends('layouts.admin')

@section('title', 'Student Report: ' . $attempt->student->name)
@section('page_title', 'Student Performance Report')

@section('content')
<div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline">← Back to Results Directory</a>
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="button" onclick="window.print();" class="btn btn-primary">
            <span>🖨️ Print Score Sheet / Save as PDF</span>
        </button>
    </div>
</div>

<div class="result-card">
    <div class="result-hero">
        <div>
            <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); margin-bottom: 8px;">
                Administrator Evaluation Sheet
            </span>
            <h1 style="font-size: 24px; font-weight: 800; color: #ffffff; margin-bottom: 4px;">{{ $attempt->exam->title }}</h1>
            <p style="font-size: 14px; opacity: 0.9; color: #d1fae5;">{{ $attempt->exam->subject }}</p>
        </div>

        <div class="result-score-block">
            <div class="result-score-big">{{ $attempt->score }} <span style="font-size: 20px; font-weight: 500;">/ {{ $attempt->max_score }}</span></div>
            <div class="result-score-label">Final Awarded Score</div>
        </div>
    </div>

    <!-- Candidate Header -->
    <div style="background: #fafdfa; border-bottom: 1px solid var(--border); padding: 18px 24px;">
        <div class="form-row">
            <div class="form-col">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Candidate</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--primary);">{{ $attempt->student->name }}</div>
                <div style="font-size: 12px; color: var(--text-muted);">{{ $attempt->student->email }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Student ID</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $attempt->student->student_id }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Academic Batch</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $attempt->student->batch->full_name }}</div>
            </div>
            <div class="form-col">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Status & Timing</div>
                <div>
                    <x-status-badge :status="$attempt->status" />
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">Duration: {{ $attempt->time_taken_formatted }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="result-stats-row">
        <div class="result-stat-box">
            <div class="result-stat-num">{{ $attempt->total_questions }}</div>
            <div class="result-stat-title">Total MCQs</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--primary);">{{ $attempt->answered_count }}</div>
            <div class="result-stat-title">Answered</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--text-muted);">{{ $attempt->unanswered_count }}</div>
            <div class="result-stat-title">Unanswered</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--success);">{{ $attempt->correct_count }}</div>
            <div class="result-stat-title">Correct</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: var(--danger);">{{ $attempt->incorrect_count }}</div>
            <div class="result-stat-title">Incorrect</div>
        </div>
        <div class="result-stat-box">
            <div class="result-stat-num" style="color: {{ $attempt->percentage >= 40 ? 'var(--success)' : 'var(--danger)' }};">
                {{ $attempt->percentage }}%
            </div>
            <div class="result-stat-title">Percentage</div>
        </div>
    </div>

    <!-- Questions Detailed Evaluation Matrix -->
    <div class="card-body" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
            Question-by-Question Evaluation Audit
        </h3>

        @php
            $answersKeyed = $attempt->answers->keyBy('question_id');
        @endphp

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Question Statement</th>
                        <th>Student Response</th>
                        <th>Correct Key</th>
                        <th>Outcome</th>
                        <th style="text-align: right;">Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempt->exam->questions as $i => $q)
                        @php
                            $ans = $answersKeyed->get($q->id);
                            $selected = $ans ? $ans->selected_option : null;
                            $isCorrect = $ans ? $ans->is_correct : false;
                            $marks = $ans ? $ans->marks_awarded : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $i + 1 }}</strong></td>
                            <td>
                                <div style="font-weight: 500;">{{ $q->question_text }}</div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                                    (A) {{ $q->option_a }} | (B) {{ $q->option_b }} | (C) {{ $q->option_c }} | (D) {{ $q->option_d }}
                                </div>
                            </td>
                            <td>
                                @if ($selected)
                                    <strong>Option {{ $selected }}</strong>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Not Answered</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">Option {{ $q->correct_option }}</span>
                            </td>
                            <td>
                                @if (!$selected)
                                    <span class="badge badge-secondary">Unanswered</span>
                                @elseif ($isCorrect)
                                    <span class="badge badge-success">Correct ✓</span>
                                @else
                                    <span class="badge badge-danger">Incorrect ✗</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <strong>{{ $marks }}</strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print Footer Signature Area -->
    <div class="print-only-footer">
        <div style="text-align: center; width: 220px;">
            <div style="height: 50px;"></div>
            <div style="border-top: 1px solid #000; padding-top: 4px; font-size: 11pt;">Examiner Signature</div>
        </div>
        <div style="text-align: center; width: 250px;">
            <div style="height: 50px;"></div>
            <div style="border-top: 1px solid #000; padding-top: 4px; font-size: 11pt;">Controller of Examinations</div>
        </div>
    </div>
</div>
@endsection
