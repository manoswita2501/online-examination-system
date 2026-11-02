<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students with search and batch filter.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'student')->with('batch');

        // Search by name, email, or student_id
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by batch
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->input('batch_id'));
        }

        $students = $query->orderBy('student_id')->paginate(15)->withQueryString();
        $batches = Batch::orderBy('course_name')->get();

        return view('admin.students.index', compact('students', 'batches'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(Request $request): View
    {
        $batches = Batch::orderBy('course_name')->orderBy('semester')->get();
        $selectedBatchId = $request->input('batch_id');

        return view('admin.students.create', compact('batches', 'selectedBatchId'));
    }

    /**
     * Store a newly created student in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', 'unique:users,student_id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'batch_id' => ['required', 'exists:batches,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'password.min' => 'Student password must be at least 8 characters long.',
            'batch_id.required' => 'Please assign the student to an academic batch.',
            'student_id.unique' => 'A student with this Student ID already exists.',
            'email.unique' => 'This email address is already registered to another user.',
        ]);

        User::create([
            'name' => $validated['name'],
            'student_id' => strtoupper(trim($validated['student_id'])),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'batch_id' => $validated['batch_id'],
            'role' => 'student', // Enforce student role
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.students.index')->with('success', "Student account for {$validated['name']} created successfully.");
    }

    /**
     * Display the specified student's details and examination attempts.
     */
    public function show(User $student): View
    {
        abort_unless($student->isStudent(), 404);

        $student->load(['batch', 'attempts' => function ($q) {
            $q->with('exam')->latest('submitted_at');
        }]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the student.
     */
    public function edit(User $student): View
    {
        abort_unless($student->isStudent(), 404);

        $batches = Batch::orderBy('course_name')->get();

        return view('admin.students.edit', compact('student', 'batches'));
    }

    /**
     * Update the specified student in database.
     */
    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->isStudent(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($student->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'batch_id' => ['required', 'exists:batches,id'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'password.min' => 'New password must be at least 8 characters long if provided.',
            'batch_id.required' => 'Please assign the student to a valid academic batch.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'student_id' => strtoupper(trim($validated['student_id'])),
            'email' => strtolower(trim($validated['email'])),
            'batch_id' => $validated['batch_id'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $student->update($updateData);

        return redirect()->route('admin.students.show', $student)->with('success', 'Student details updated successfully.');
    }

    /**
     * Toggle student activation/deactivation.
     */
    public function toggleStatus(User $student): RedirectResponse
    {
        abort_unless($student->isStudent(), 404);

        $student->is_active = !$student->is_active;
        $student->save();

        $statusStr = $student->is_active ? 'reactivated' : 'deactivated';
        return back()->with('success', "Student account {$student->name} has been {$statusStr}.");
    }

    /**
     * Delete student if no attempts exist; otherwise warn to deactivate.
     */
    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->isStudent(), 404);

        $attemptsCount = $student->attempts()->count();
        if ($attemptsCount > 0) {
            return back()->with('error', "Cannot delete student {$student->name} because they have {$attemptsCount} examination attempt(s) on record. Please deactivate their account instead.");
        }

        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student record deleted successfully.');
    }
}
