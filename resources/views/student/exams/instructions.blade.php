@extends('layouts.student')

@section('title', 'Exam Instructions: ' . $exam->title)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline">← Back to Dashboard</a>
    </div>

    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: #ffffff; padding: 24px 28px;">
            <div>
                <h1 style="font-size: 20px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">{{ $exam->title }}</h1>
                <div style="font-size: 14px; color: #d1fae5;">{{ $exam->subject }}</div>
            </div>
            <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); font-size: 12px;">
                {{ $exam->duration_minutes }} Minutes
            </span>
        </div>

        <div class="card-body" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                Examination Specifications
            </h3>

            <div class="form-row" style="background: #fafdfa; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px; margin-bottom: 24px;">
                <div class="form-col">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Candidate Name</div>
                    <div style="font-weight: 600;">{{ $student->name }} ({{ $student->student_id }})</div>
                </div>
                <div class="form-col">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Batch</div>
                    <div style="font-weight: 600;">{{ $exam->batch->course_name }} - {{ $exam->batch->section }}</div>
                </div>
                <div class="form-col">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total MCQs</div>
                    <div style="font-weight: 700; color: var(--primary);">{{ $totalQuestions }} Questions</div>
                </div>
                <div class="form-col">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Maximum Marks</div>
                    <div style="font-weight: 700; color: var(--primary);">{{ $exam->total_marks }} Marks ({{ $exam->marks_per_question }} mk/Q)</div>
                </div>
            </div>

            <h3 style="font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 12px;">
                Important Candidate Guidelines & Rules
            </h3>

            <div style="line-height: 1.8; color: var(--text-secondary); margin-bottom: 28px;">
                <ol style="margin-left: 20px;">
                    <li><strong>Answering & Saving:</strong> You must select an option (A, B, C, or D) and explicitly click the <strong>"Save Answer"</strong> button to persist your response in the database.</li>
                    <li><strong>Unsaved Changes:</strong> Changing an option without clicking "Save Answer" will not be saved if you navigate away or the exam ends.</li>
                    <li><strong>Navigation:</strong> You can navigate freely between questions using the <em>Previous</em> and <em>Next</em> buttons or directly through the Question Palette numbers.</li>
                    <li><strong>Marking Scheme:</strong> Each correct response is awarded <strong>{{ $exam->marks_per_question }} mark(s)</strong>. Unanswered and incorrect questions receive <strong>0 marks</strong>. There is no negative marking.</li>
                    <li><strong>Countdown Timer & Expiry:</strong> A visible timer will count down your remaining time. When the timer reaches 00:00, your exam will be <strong>automatically submitted</strong> with whatever answers have been saved to the database.</li>
                    <li><strong>Single Attempt Rule:</strong> You are allowed exactly one attempt for this examination. Once submitted, you cannot retake the test.</li>
                </ol>

                @if ($exam->instructions)
                    <div style="margin-top: 16px; padding: 14px; background: #f0fdf4; border: 1px solid var(--accent-border); border-radius: var(--radius-sm);">
                        <strong style="color: var(--primary);">Examiner's Specific Instructions:</strong><br>
                        <span style="white-space: pre-line;">{{ $exam->instructions }}</span>
                    </div>
                @endif
            </div>

            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid var(--warning); padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                <div style="font-size: 13px; color: #92400e;">
                    <strong>Notice:</strong> Once you click "Start Examination", your timer starts immediately on the server. Make sure you have a reliable network connection before starting.
                </div>
            </div>

            <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                @csrf
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        Cancel & Return
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Ready to begin? Your countdown timer will start immediately.');">
                        Start Examination Now →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
