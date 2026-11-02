<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display overall examinations results overview with filters.
     */
    public function index(Request $request): View
    {
        $query = ExamAttempt::with(['student.batch', 'exam'])
            ->whereIn('status', ['submitted', 'auto_submitted']);

        // Filter by Exam
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->input('exam_id'));
        }

        // Filter by Batch
        if ($request->filled('batch_id')) {
            $batchId = $request->input('batch_id');
            $query->whereHas('student', function ($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            });
        }

        // Filter by Student name or ID
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attempts = $query->orderBy('submitted_at', 'desc')->paginate(15)->withQueryString();
        $exams = Exam::orderBy('exam_date', 'desc')->get();
        $batches = Batch::orderBy('course_name')->get();

        return view('admin.reports.index', compact('attempts', 'exams', 'batches'));
    }

    /**
     * Display individual student examination performance report.
     */
    public function studentReport(ExamAttempt $attempt): View
    {
        $attempt->load(['student.batch', 'exam.questions', 'answers.question']);

        return view('admin.reports.student', compact('attempt'));
    }

    /**
     * Display consolidated batch-wise examination report.
     */
    public function consolidatedReport(Exam $exam): View
    {
        $exam->load('batch');
        $batch = $exam->batch;

        // All attempts for this exam
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with('student.batch')
            ->get()
            ->keyBy('student_id');

        $attemptedStudentIds = $attempts->keys();

        // All students belonging to this exam's batch, plus any candidate who attempted it
        $assignedStudents = User::where('role', 'student')
            ->where(function ($q) use ($batch, $attemptedStudentIds) {
                if ($batch) {
                    $q->where('batch_id', $batch->id);
                }
                $q->orWhereIn('id', $attemptedStudentIds);
            })
            ->with('batch')
            ->orderBy('student_id')
            ->get();

        $assignedCount = $assignedStudents->count();
        $submittedAttempts = $attempts->where('status', 'submitted')->count();
        $autoSubmittedAttempts = $attempts->where('status', 'auto_submitted')->count();
        $completedAttempts = $attempts->whereIn('status', ['submitted', 'auto_submitted']);
        $completedCount = $completedAttempts->count();
        $unattemptedCount = max(0, $assignedCount - $completedCount);

        // Score analytics
        $averageScore = $completedCount > 0 ? round($completedAttempts->avg('score'), 2) : 0;
        $highestScore = $completedCount > 0 ? $completedAttempts->max('score') : 0;
        $lowestScore = $completedCount > 0 ? $completedAttempts->min('score') : 0;

        return view('admin.reports.consolidated', compact(
            'exam',
            'batch',
            'assignedStudents',
            'attempts',
            'assignedCount',
            'submittedAttempts',
            'autoSubmittedAttempts',
            'completedCount',
            'unattemptedCount',
            'averageScore',
            'highestScore',
            'lowestScore'
        ));
    }
}
