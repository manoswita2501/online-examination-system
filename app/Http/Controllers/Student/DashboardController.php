<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display student dashboard with batch-filtered examinations.
     */
    public function index(): View
    {
        $student = Auth::user()->load('batch');
        $batch = $student->batch;

        // Fetch all published exams for all students to participate in real time
        $publishedExams = Exam::where('status', 'published')
            ->with('batch')
            ->withCount('questions')
            ->orderBy('exam_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // Get student's existing attempts keyed by exam_id
        $attempts = ExamAttempt::where('student_id', $student->id)
            ->with('exam')
            ->get()
            ->keyBy('exam_id');

        $now = now();
        $availableExams = collect();
        $upcomingExams = collect();
        $inProgressAttempts = collect();
        $completedAttempts = collect();

        foreach ($publishedExams as $exam) {
            $attempt = $attempts->get($exam->id);
            $startDateTime = $exam->start_date_time;

            if ($attempt) {
                if ($attempt->isInProgress()) {
                    // Check if attempt has expired
                    if ($attempt->hasExpired()) {
                        // Mark as auto_submitted through evaluation service
                        app(\App\Services\ExamEvaluationService::class)->finalizeAttempt($attempt, 'auto_submitted');
                        $completedAttempts->push($attempt->fresh());
                    } else {
                        $inProgressAttempts->push($attempt);
                    }
                } else {
                    $completedAttempts->push($attempt);
                }
            } else {
                // No attempt yet
                if ($now->lessThan($startDateTime)) {
                    $upcomingExams->push($exam);
                } else {
                    // Currently available to take in real time
                    $availableExams->push($exam);
                }
            }
        }

        return view('student.dashboard', compact(
            'student',
            'batch',
            'availableExams',
            'upcomingExams',
            'inProgressAttempts',
            'completedAttempts'
        ));
    }
}
