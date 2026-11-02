<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Services\ExamEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExamAttemptController extends Controller
{
    /**
     * Display the interactive examination interface.
     */
    public function show(ExamAttempt $attempt): View|RedirectResponse
    {
        // 1. Verify attempt ownership
        if ($attempt->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this examination session.');
        }

        // 2. If attempt is already submitted, redirect to result
        if ($attempt->isSubmitted()) {
            return redirect()->route('student.results.show', $attempt);
        }

        // 3. Server-side deadline check
        if ($attempt->hasExpired()) {
            app(ExamEvaluationService::class)->finalizeAttempt($attempt, 'auto_submitted');
            return redirect()->route('student.results.show', $attempt)
                ->with('info', 'Your allowed examination time has expired. Your answers have been automatically submitted.');
        }

        $exam = $attempt->exam;

        // Security requirement: NEVER send correct_option to the browser during the exam!
        $questions = $exam->questions()
            ->select(['id', 'exam_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d'])
            ->get();

        // Retrieve student's saved answers keyed by question_id
        $savedAnswers = $attempt->answers()
            ->pluck('selected_option', 'question_id')
            ->toArray();

        $remainingSeconds = $attempt->remaining_seconds;
        $student = Auth::user();

        return view('student.exams.take', compact(
            'attempt',
            'exam',
            'questions',
            'savedAnswers',
            'remainingSeconds',
            'student'
        ));
    }

    /**
     * Explicitly save or update student answer for a question.
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt): JsonResponse
    {
        // 1. Verify ownership
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized attempt.'], 403);
        }

        // 2. Verify attempt status
        if (!$attempt->isInProgress()) {
            return response()->json([
                'success' => false, 
                'message' => 'This examination has already been finalized.',
                'redirect' => route('student.results.show', $attempt)
            ], 400);
        }

        // 3. Verify server-side deadline
        if ($attempt->hasExpired()) {
            app(ExamEvaluationService::class)->finalizeAttempt($attempt, 'auto_submitted');
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => 'Time expired. The exam has been automatically submitted.',
                'redirect' => route('student.results.show', $attempt)
            ], 400);
        }

        // 4. Validate input
        $validated = $request->validate([
            'question_id' => ['required', 'integer'],
            'selected_option' => ['required', 'string', 'in:A,B,C,D'],
        ]);

        // 5. Verify question belongs to this exam
        $question = Question::where('id', $validated['question_id'])
            ->where('exam_id', $attempt->exam_id)
            ->first();

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Invalid question for this exam.'], 422);
        }

        // 6. Save or update answer in database
        StudentAnswer::updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'selected_option' => strtoupper($validated['selected_option']),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Answer saved successfully.',
            'question_id' => $question->id,
            'selected_option' => strtoupper($validated['selected_option']),
        ]);
    }

    /**
     * Final submission of the examination by student or automatic timer trigger.
     */
    public function submit(Request $request, ExamAttempt $attempt, ExamEvaluationService $evaluationService): RedirectResponse|JsonResponse
    {
        // 1. Verify ownership
        if ($attempt->student_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // 2. Finalize attempt (if not already finalized)
        $finalStatus = $attempt->hasExpired() ? 'auto_submitted' : 'submitted';
        $evaluationService->finalizeAttempt($attempt, $finalStatus);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Examination submitted successfully.',
                'redirect' => route('student.results.show', $attempt),
            ]);
        }

        return redirect()->route('student.results.show', $attempt)
            ->with('success', 'Your examination has been submitted and evaluated successfully!');
    }
}
