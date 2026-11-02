<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResultController extends Controller
{
    /**
     * Display student's examination result sheet.
     */
    public function show(ExamAttempt $attempt): View|RedirectResponse
    {
        // 1. Verify ownership (Student can only see their own result)
        if ($attempt->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this result report.');
        }

        // 2. Ensure attempt is finalized
        if (!$attempt->isSubmitted()) {
            return redirect()->route('student.attempts.show', $attempt);
        }

        $attempt->load(['student.batch', 'exam']);
        $student = $attempt->student;
        $exam = $attempt->exam;

        return view('student.results.show', compact('attempt', 'student', 'exam'));
    }
}
