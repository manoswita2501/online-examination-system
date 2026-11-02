@extends('layouts.student')

@section('title', 'Exam Result: ' . $exam->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Action buttons above report (hidden on print) -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline">
            ← Back to Student Dashboard
        </a>
        <div style="display: flex; gap: 10px;">
            <form action="{{ route('student.exams.start', $exam) }}" method="POST" style="display: inline;" onsubmit="return confirm('Retake this examination for practice? A new attempt will be started.');">
                @csrf
                <input type="hidden" name="retake" value="1">
                <button type="submit" class="btn btn-secondary">
                    <span>🔄 Retake Exam (Practice Demo)</span>
                </button>
            </form>
            <button type="button" onclick="window.print();" class="btn btn-primary">
                <span>🖨️ Print Result / Save as PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Grade Card Sheet -->
    <div class="result-card">
        <!-- Hero Header -->
        <div class="result-hero">
            <div>
                <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); margin-bottom: 8px;">
                    Official Examination Score Sheet
                </span>
                <h1 style="font-size: 24px; font-weight: 800; color: #ffffff; margin-bottom: 4px;">{{ $exam->title }}</h1>
                <p style="font-size: 14px; opacity: 0.9; color: #d1fae5;">
                    {{ $exam->subject }} • Exam Date: {{ $exam->exam_date->format('d M Y') }}
                </p>
            </div>

            <div class="result-score-block">
                <div class="result-score-big">{{ $attempt->score }} <span style="font-size: 20px; font-weight: 500;">/ {{ $attempt->max_score }}</span></div>
                <div class="result-score-label">Total Marks Obtained</div>
            </div>
        </div>

        <!-- Student Details Strip -->
        <div style="background: #fafdfa; border-bottom: 1px solid var(--border); padding: 18px 24px;">
            <div class="form-row">
                <div class="form-col">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Candidate Name</div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--primary);">{{ $student->name }}</div>
                </div>
                <div class="form-col">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Student ID / Roll No</div>
                    <div style="font-size: 15px; font-weight: 600;">{{ $student->student_id }}</div>
                </div>
                <div class="form-col">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Academic Batch</div>
                    <div style="font-size: 15px; font-weight: 600;">{{ $student->batch->full_name }}</div>
                </div>
                <div class="form-col">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Submission Status</div>
                    <div>
                        <x-status-badge :status="$attempt->status" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Statistics Strip -->
        <div class="result-stats-row">
            <div class="result-stat-box">
                <div class="result-stat-num">{{ $attempt->total_questions }}</div>
                <div class="result-stat-title">Total Questions</div>
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

        <!-- Evaluation Breakdown Table -->
        <div class="card-body" style="padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                Examination Assessment Breakdown
            </h3>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Evaluation Metric</th>
                            <th>Value</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Marks per Correct Answer</strong></td>
                            <td>{{ $exam->marks_per_question }} Mark(s)</td>
                            <td>Uniform weight per multiple-choice item</td>
                        </tr>
                        <tr>
                            <td><strong>Maximum Possible Score</strong></td>
                            <td>{{ $attempt->max_score }} Marks</td>
                            <td>Sum of all questions multiplied by question weight</td>
                        </tr>
                        <tr>
                            <td><strong>Marks Obtained</strong></td>
                            <td><strong style="color: var(--primary); font-size: 16px;">{{ $attempt->score }} Marks</strong></td>
                            <td>Calculated via server-side verification</td>
                        </tr>
                        <tr>
                            <td><strong>Percentage Score</strong></td>
                            <td><strong>{{ $attempt->percentage }}%</strong></td>
                            <td>({{ $attempt->score }} / {{ $attempt->max_score }}) × 100</td>
                        </tr>
                        <tr>
                            <td><strong>Time Started</strong></td>
                            <td>{{ $attempt->started_at->format('d M Y, h:i:s A') }}</td>
                            <td>Session initialization timestamp</td>
                        </tr>
                        <tr>
                            <td><strong>Time Submitted</strong></td>
                            <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, h:i:s A') : 'N/A' }}</td>
                            <td>{{ $attempt->status === 'auto_submitted' ? 'Automatic system submission upon expiry' : 'Manual candidate final submission' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Duration Taken</strong></td>
                            <td><strong>{{ $attempt->time_taken_formatted }}</strong></td>
                            <td>Allowed duration: {{ $exam->duration_minutes }} minutes</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 24px; padding: 14px; background: #fafdfa; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 12px; color: var(--text-muted);">
                <strong>Academic Integrity Note:</strong> As per college policy, question-wise answer keys are withheld to protect examination integrity. For queries regarding your marks, please reach out to your course instructor.
            </div>
        </div>

        <!-- Print Footer Signature Area -->
        <div class="print-only-footer">
            <div style="text-align: center; width: 200px;">
                <div style="height: 50px;"></div>
                <div style="border-top: 1px solid #000; padding-top: 4px; font-size: 11pt;">Candidate Signature</div>
            </div>
            <div style="text-align: center; width: 250px;">
                <div style="height: 50px;"></div>
                <div style="border-top: 1px solid #000; padding-top: 4px; font-size: 11pt;">Controller of Examinations</div>
            </div>
        </div>
    </div>
</div>
@endsection
