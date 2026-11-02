<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Show instructions and start button for an examination.
     */
    public function instructions(Exam $exam): View|RedirectResponse
    {
        $student = Auth::user();

        // 1. Verify publication status
        if (!$exam->isPublished() || $exam->isClosed()) {
            abort(404, 'Examination not found or not currently active.');
        }

        // 2. Check for existing attempt
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingAttempt && !request()->boolean('retake')) {
            if ($existingAttempt->isSubmitted()) {
                return redirect()->route('student.results.show', $existingAttempt)
                    ->with('info', 'You have already completed this examination. Here is your result.');
            }

            if ($existingAttempt->isInProgress()) {
                if ($existingAttempt->hasExpired()) {
                    app(ExamEvaluationService::class)->finalizeAttempt($existingAttempt, 'auto_submitted');
                    return redirect()->route('student.results.show', $existingAttempt)
                        ->with('info', 'Your examination duration has expired. Answers have been submitted.');
                }

                // Resume in-progress attempt
                return redirect()->route('student.attempts.show', $existingAttempt)
                    ->with('info', 'Resuming your active examination session.');
            }
        }

        // 3. Verify schedule
        $now = now();
        if ($now->lessThan($exam->start_date_time)) {
            return redirect()->route('student.dashboard')
                ->with('error', "This examination is scheduled for {$exam->exam_date->format('d M Y')} at " . date('h:i A', strtotime($exam->start_time)) . '. It cannot be started yet.');
        }

        $exam->load('batch');
        $totalQuestions = $exam->questions()->count();

        return view('student.exams.instructions', compact('exam', 'student', 'totalQuestions'));
    }

    /**
     * Start the examination and create an ExamAttempt record.
     */
    public function start(Request $request, Exam $exam): RedirectResponse
    {
        $student = Auth::user();

        // 1. Verify publication status
        if (!$exam->isPublished() || $exam->isClosed()) {
            abort(404, 'Examination not available or has been closed.');
        }

        // 2. Verify schedule
        $now = now();
        if ($now->lessThan($exam->start_date_time)) {
            return redirect()->route('student.dashboard')->with('error', 'Examination schedule has not arrived yet.');
        }

        // 3. Handle retake request (resets previous attempt for demo/practice)
        if ($request->boolean('retake')) {
            $existing = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existing) {
                $existing->answers()->delete();
                $existing->delete();
            }
        }

        // 4. Concurrency-safe attempt creation or retrieval
        $attempt = DB::transaction(function () use ($exam, $student) {
            $existing = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            return ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'status' => 'in_progress',
                'total_questions' => $exam->questions()->count(),
            ]);
        });

        if ($attempt->isSubmitted()) {
            return redirect()->route('student.results.show', $attempt);
        }

        if ($attempt->hasExpired()) {
            app(ExamEvaluationService::class)->finalizeAttempt($attempt, 'auto_submitted');
            return redirect()->route('student.results.show', $attempt)
                ->with('info', 'Your examination duration has expired.');
        }

        return redirect()->route('student.attempts.show', $attempt);
    }
}
