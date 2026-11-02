<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Display a listing of examinations.
     */
    public function index(Request $request): View
    {
        $query = Exam::with('batch')->withCount(['questions', 'attempts']);

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->input('batch_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $exams = $query->orderBy('exam_date', 'desc')->orderBy('start_time', 'desc')->paginate(12)->withQueryString();
        $batches = Batch::orderBy('course_name')->get();

        return view('admin.exams.index', compact('exams', 'batches'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create(Request $request): View
    {
        $batches = Batch::orderBy('course_name')->orderBy('semester')->get();
        $selectedBatchId = $request->input('batch_id');

        return view('admin.exams.create', compact('batches', 'selectedBatchId'));
    }

    /**
     * Store a newly created exam and its questions using a database transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'batch_id' => ['required', 'exists:batches,id'],
            'instructions' => ['nullable', 'string'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'marks_per_question' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:draft,published'],

            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.option_a' => ['required', 'string'],
            'questions.*.option_b' => ['required', 'string'],
            'questions.*.option_c' => ['required', 'string'],
            'questions.*.option_d' => ['required', 'string'],
            'questions.*.correct_option' => ['required', 'in:A,B,C,D'],
        ], [
            'questions.required' => 'Please add at least one multiple-choice question to this examination.',
            'questions.min' => 'An examination must contain at least one question.',
            'questions.*.question_text.required' => 'Every question requires a question statement.',
            'questions.*.option_a.required' => 'Option A is required for all questions.',
            'questions.*.option_b.required' => 'Option B is required for all questions.',
            'questions.*.option_c.required' => 'Option C is required for all questions.',
            'questions.*.option_d.required' => 'Option D is required for all questions.',
            'questions.*.correct_option.required' => 'Select the correct option (A, B, C, or D) for every question.',
        ]);

        DB::transaction(function () use ($validated) {
            $exam = Exam::create([
                'title' => $validated['title'],
                'subject' => $validated['subject'],
                'batch_id' => $validated['batch_id'],
                'instructions' => $validated['instructions'],
                'exam_date' => $validated['exam_date'],
                'start_time' => $validated['start_time'],
                'duration_minutes' => $validated['duration_minutes'],
                'marks_per_question' => $validated['marks_per_question'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['questions'] as $qData) {
                $exam->questions()->create([
                    'question_text' => $qData['question_text'],
                    'option_a' => $qData['option_a'],
                    'option_b' => $qData['option_b'],
                    'option_c' => $qData['option_c'],
                    'option_d' => $qData['option_d'],
                    'correct_option' => strtoupper($qData['correct_option']),
                ]);
            }
        });

        $message = $validated['status'] === 'published' 
            ? 'Examination created and published successfully. Eligible students can now see it.' 
            : 'Examination saved as draft. It remains hidden from students until published.';

        return redirect()->route('admin.exams.index')->with('success', $message);
    }

    /**
     * Display the specified exam details, questions, and attempt summaries.
     */
    public function show(Exam $exam): View
    {
        $exam->load([
            'batch',
            'questions',
            'attempts' => function ($q) {
                $q->with('student')->latest('submitted_at');
            }
        ]);

        $attemptsCount = $exam->attempts->count();
        $submittedCount = $exam->attempts->whereIn('status', ['submitted', 'auto_submitted'])->count();

        return view('admin.exams.show', compact('exam', 'attemptsCount', 'submittedCount'));
    }

    /**
     * Show the form for editing an exam (only if no attempts started).
     */
    public function edit(Exam $exam): View|RedirectResponse
    {
        if ($exam->attempts()->count() > 0) {
            return redirect()->route('admin.exams.show', $exam)->with('error', 'Cannot edit questions or marking rules after an examination attempt has already begun.');
        }

        $batches = Batch::orderBy('course_name')->get();
        $exam->load('questions');

        return view('admin.exams.edit', compact('exam', 'batches'));
    }

    /**
     * Update the specified exam in database.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        if ($exam->attempts()->count() > 0) {
            return redirect()->route('admin.exams.show', $exam)->with('error', 'Cannot edit examination details after students have started taking it.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'batch_id' => ['required', 'exists:batches,id'],
            'instructions' => ['nullable', 'string'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'marks_per_question' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:draft,published,closed'],

            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.option_a' => ['required', 'string'],
            'questions.*.option_b' => ['required', 'string'],
            'questions.*.option_c' => ['required', 'string'],
            'questions.*.option_d' => ['required', 'string'],
            'questions.*.correct_option' => ['required', 'in:A,B,C,D'],
        ]);

        DB::transaction(function () use ($exam, $validated) {
            $exam->update([
                'title' => $validated['title'],
                'subject' => $validated['subject'],
                'batch_id' => $validated['batch_id'],
                'instructions' => $validated['instructions'],
                'exam_date' => $validated['exam_date'],
                'start_time' => $validated['start_time'],
                'duration_minutes' => $validated['duration_minutes'],
                'marks_per_question' => $validated['marks_per_question'],
                'status' => $validated['status'],
            ]);

            // Replace questions
            $exam->questions()->delete();

            foreach ($validated['questions'] as $qData) {
                $exam->questions()->create([
                    'question_text' => $qData['question_text'],
                    'option_a' => $qData['option_a'],
                    'option_b' => $qData['option_b'],
                    'option_c' => $qData['option_c'],
                    'option_d' => $qData['option_d'],
                    'correct_option' => strtoupper($qData['correct_option']),
                ]);
            }
        });

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Examination updated successfully.');
    }

    /**
     * Publish a draft examination.
     */
    public function publish(Exam $exam): RedirectResponse
    {
        if ($exam->questions()->count() === 0) {
            return back()->with('error', 'Cannot publish an examination with zero questions.');
        }

        $exam->update(['status' => 'published']);

        return back()->with('success', "Examination '{$exam->title}' has been published and is now active for batch {$exam->batch->course_name}.");
    }

    /**
     * Close an examination.
     */
    public function close(Exam $exam): RedirectResponse
    {
        $exam->update(['status' => 'closed']);

        return back()->with('info', "Examination '{$exam->title}' has been closed. Students can no longer start this exam.");
    }

    /**
     * Delete an examination if no student attempts exist.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'Cannot delete this examination because students have already attempted it.');
        }

        DB::transaction(function () use ($exam) {
            $exam->questions()->delete();
            $exam->delete();
        });

        return redirect()->route('admin.exams.index')->with('success', 'Examination deleted successfully.');
    }
}
