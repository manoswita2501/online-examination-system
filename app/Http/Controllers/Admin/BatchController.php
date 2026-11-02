<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatchController extends Controller
{
    /**
     * Display a listing of academic batches with student and exam counts.
     */
    public function index(): View
    {
        $batches = Batch::withCount(['students', 'exams'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('course_name')
            ->orderBy('semester')
            ->get();

        return view('admin.batches.index', compact('batches'));
    }

    /**
     * Show the form for creating a new batch.
     */
    public function create(): View
    {
        return view('admin.batches.create');
    }

    /**
     * Store a newly created batch in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_name' => ['required', 'string', 'max:100'],
            'department' => ['required', 'string', 'max:150'],
            'semester' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:20'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        // Prevent duplicate batch creation
        $exists = Batch::where('course_name', $validated['course_name'])
            ->where('semester', $validated['semester'])
            ->where('section', $validated['section'])
            ->where('academic_year', $validated['academic_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'course_name' => 'A batch with this exact Course, Semester, Section, and Academic Year already exists.',
            ]);
        }

        Batch::create($validated);

        return redirect()->route('admin.batches.index')->with('success', 'Academic batch created successfully.');
    }

    /**
     * Display the specified batch with students and exams.
     */
    public function show(Batch $batch): View
    {
        $batch->load([
            'students' => function ($query) {
                $query->orderBy('student_id');
            },
            'exams' => function ($query) {
                $query->withCount('questions')->orderBy('exam_date', 'desc');
            }
        ]);

        return view('admin.batches.show', compact('batch'));
    }

    /**
     * Show the form for editing the batch.
     */
    public function edit(Batch $batch): View
    {
        return view('admin.batches.edit', compact('batch'));
    }

    /**
     * Update the specified batch in database.
     */
    public function update(Request $request, Batch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'course_name' => ['required', 'string', 'max:100'],
            'department' => ['required', 'string', 'max:150'],
            'semester' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:20'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        // Prevent duplicate excluding current batch
        $exists = Batch::where('course_name', $validated['course_name'])
            ->where('semester', $validated['semester'])
            ->where('section', $validated['section'])
            ->where('academic_year', $validated['academic_year'])
            ->where('id', '!=', $batch->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'course_name' => 'Another batch already has this identical Course, Semester, Section, and Academic Year.',
            ]);
        }

        $batch->update($validated);

        return redirect()->route('admin.batches.show', $batch)->with('success', 'Batch information updated successfully.');
    }

    /**
     * Remove the specified batch if no dependencies exist.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        $studentsCount = $batch->students()->count();
        $examsCount = $batch->exams()->count();

        if ($studentsCount > 0 || $examsCount > 0) {
            return back()->with('error', "Cannot delete batch '{$batch->full_name}'. It has {$studentsCount} enrolled student(s) and {$examsCount} scheduled examination(s).");
        }

        $batch->delete();

        return redirect()->route('admin.batches.index')->with('success', 'Batch removed successfully.');
    }
}
