<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display administrator dashboard with real database metrics.
     */
    public function index(): View
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalBatches = Batch::count();
        $totalExams = Exam::count();
        
        $publishedExams = Exam::where('status', 'published')->count();
        $draftExams = Exam::where('status', 'draft')->count();
        $closedExams = Exam::where('status', 'closed')->count();

        $completedAttemptsCount = ExamAttempt::whereIn('status', ['submitted', 'auto_submitted'])->count();

        // Recent student exam attempts
        $recentAttempts = ExamAttempt::with(['student.batch', 'exam'])
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->latest('submitted_at')
            ->take(6)
            ->get();

        // Upcoming examinations
        $upcomingExams = Exam::with('batch')
            ->where('status', 'published')
            ->whereDate('exam_date', '>=', now()->toDateString())
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalBatches',
            'totalExams',
            'publishedExams',
            'draftExams',
            'closedExams',
            'completedAttemptsCount',
            'recentAttempts',
            'upcomingExams'
        ));
    }
}
