<?php

use App\Http\Controllers\Admin\BatchController as AdminBatchController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamAttemptController as StudentExamAttemptController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\ResultController as StudentResultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Student Authentication
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [StudentLoginController::class, 'logout'])->name('logout');
});

// Administrator Authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected Student Routes (role: student)
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Examination Workflow
    Route::get('/exams/{exam}', [StudentExamController::class, 'instructions'])->name('exams.instructions');
    Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');

    // Interactive Exam Screen, Answer Persisting & Submission
    Route::get('/attempts/{attempt}', [StudentExamAttemptController::class, 'show'])->name('attempts.show');
    Route::post('/attempts/{attempt}/answers', [StudentExamAttemptController::class, 'saveAnswer'])->name('attempts.answers');
    Route::post('/attempts/{attempt}/submit', [StudentExamAttemptController::class, 'submit'])->name('attempts.submit');

    // Results & Score Sheet
    Route::get('/results/{attempt}', [StudentResultController::class, 'show'])->name('results.show');
});

/*
|--------------------------------------------------------------------------
| Protected Administrator Routes (role: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Batches CRUD
    Route::resource('batches', AdminBatchController::class);

    // Students CRUD & Status Toggle
    Route::resource('students', AdminStudentController::class);
    Route::patch('/students/{student}/toggle-status', [AdminStudentController::class, 'toggleStatus'])->name('students.toggle-status');

    // Examinations CRUD, Publishing & Closing
    Route::resource('exams', AdminExamController::class);
    Route::patch('/exams/{exam}/publish', [AdminExamController::class, 'publish'])->name('exams.publish');
    Route::patch('/exams/{exam}/close', [AdminExamController::class, 'close'])->name('exams.close');

    // Reports & Analytics
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/students/{attempt}', [AdminReportController::class, 'studentReport'])->name('reports.student');
    Route::get('/reports/exams/{exam}/consolidated', [AdminReportController::class, 'consolidatedReport'])->name('reports.consolidated');
});
