@extends('layouts.admin')

@section('title', 'Create Examination')
@section('page_title', 'Create Examination & MCQ Entry')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline">← Back to Examinations</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <div>
            <strong>Please resolve the following errors:</strong>
            <ul style="margin-left: 20px; margin-top: 6px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('admin.exams.store') }}" method="POST" id="examForm">
    @csrf

    <!-- SECTION A: Exam Details -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>📌</span>
                <span>Section A: Examination Details & Schedule</span>
            </div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="title" class="form-label">Examination Title <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            class="form-control" 
                            value="{{ old('title') }}" 
                            placeholder="e.g., Mid-Term Examination: Programming in C" 
                            required
                        >
                    </div>
                </div>

                <div class="form-col">
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject / Course Module <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="subject" 
                            id="subject" 
                            class="form-control" 
                            value="{{ old('subject') }}" 
                            placeholder="e.g., CS101 - Introduction to C Programming" 
                            required
                        >
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="batch_id" class="form-label">Target Batch <span class="required">*</span></label>
                        <select name="batch_id" id="batch_id" class="form-control" required>
                            <option value="">-- Select Target Batch --</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}" {{ (old('batch_id', $selectedBatchId) == $batch->id) ? 'selected' : '' }}>
                                    {{ $batch->course_name }} - {{ $batch->semester }} ({{ $batch->section }}, {{ $batch->academic_year }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Only students in this batch can see and take this exam.</div>
                    </div>
                </div>

                <div class="form-col">
                    <div class="form-group">
                        <label for="marks_per_question" class="form-label">Marks Per Question <span class="required">*</span></label>
                        <input 
                            type="number" 
                            name="marks_per_question" 
                            id="marks_per_question" 
                            class="form-control" 
                            value="{{ old('marks_per_question', 2) }}" 
                            min="1" 
                            max="100" 
                            required
                        >
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="exam_date" class="form-label">Exam Date <span class="required">*</span></label>
                        <input 
                            type="date" 
                            name="exam_date" 
                            id="exam_date" 
                            class="form-control" 
                            value="{{ old('exam_date', now()->format('Y-m-d')) }}" 
                            required
                        >
                    </div>
                </div>

                <div class="form-col">
                    <div class="form-group">
                        <label for="start_time" class="form-label">Start Time <span class="required">*</span></label>
                        <input 
                            type="time" 
                            name="start_time" 
                            id="start_time" 
                            class="form-control" 
                            value="{{ old('start_time', '09:00') }}" 
                            required
                        >
                    </div>
                </div>

                <div class="form-col">
                    <div class="form-group">
                        <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            name="duration_minutes" 
                            id="duration_minutes" 
                            class="form-control" 
                            value="{{ old('duration_minutes', 30) }}" 
                            min="1" 
                            max="600" 
                            required
                        >
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="instructions" class="form-label">Examination Instructions & Guidelines</label>
                <textarea 
                    name="instructions" 
                    id="instructions" 
                    rows="3" 
                    class="form-control" 
                    placeholder="Enter instructions for students (e.g., 1. All questions are mandatory. 2. Click Save Answer after every choice.)..."
                >{{ old('instructions', "1. All questions are multiple choice.\n2. Click 'Save Answer' to store your choice for each question.\n3. Timer will automatically submit your responses if time runs out.") }}</textarea>
            </div>
        </div>
    </div>

    <!-- SECTION B: Multiple-Choice Questions Entry -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>❓</span>
                <span>Section B: Multiple Choice Questions (MCQs)</span>
            </div>
            <button type="button" id="addQuestionBtn" class="btn btn-accent btn-sm">
                <span>➕ Add Another Question</span>
            </button>
        </div>

        <div class="card-body" id="questionsContainer">
            <!-- Questions will be dynamically rendered and added here -->
        </div>

        <div class="card-footer" style="background: #f8faf8; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 13px; color: var(--text-muted);">
                Total Questions: <strong id="questionCounter">0</strong>
            </span>
            <button type="button" id="addQuestionFooterBtn" class="btn btn-secondary btn-sm">
                <span>➕ Add Question</span>
            </button>
        </div>
    </div>

    <!-- SECTION C: Review & Publication -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>📋</span>
                <span>Section C: Review & Save</span>
            </div>
        </div>
        <div class="card-body">
            <div style="background: var(--accent-light); border: 1px solid var(--accent-border); border-radius: var(--radius-md); padding: 18px; margin-bottom: 20px;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--primary); margin-bottom: 8px;">Examination Live Summary</h4>
                <div class="form-row" style="font-size: 13px;">
                    <div class="form-col">
                        <div>Questions Count: <strong id="summaryQCount">0</strong></div>
                        <div>Marks Per Question: <strong id="summaryMarksPerQ">2</strong></div>
                    </div>
                    <div class="form-col">
                        <div>Maximum Possible Score: <strong id="summaryMaxMarks" style="color: var(--primary); font-size: 15px;">0</strong></div>
                        <div>Duration: <strong id="summaryDuration">30 mins</strong></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Publication Option <span class="required">*</span></label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="status" value="published" checked style="accent-color: var(--primary);">
                        <span><strong>Publish Immediately</strong> (Available to eligible students on scheduled date)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="status" value="draft" style="accent-color: var(--primary);">
                        <span><strong>Save as Draft</strong> (Hidden from students until manually published)</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">Save Examination</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let questionIndex = 0;
    const container = document.getElementById('questionsContainer');
    const counterDisplay = document.getElementById('questionCounter');
    const summaryQCount = document.getElementById('summaryQCount');
    const summaryMarksPerQ = document.getElementById('summaryMarksPerQ');
    const summaryMaxMarks = document.getElementById('summaryMaxMarks');
    const summaryDuration = document.getElementById('summaryDuration');
    const marksInput = document.getElementById('marks_per_question');
    const durationInput = document.getElementById('duration_minutes');

    // Initial question template
    function createQuestionBlock(index, text = '', optA = '', optB = '', optC = '', optD = '', correct = 'A') {
        const div = document.createElement('div');
        div.className = 'card question-card';
        div.style.marginBottom = '20px';
        div.style.border = '1.5px solid var(--border)';
        div.dataset.index = index;

        div.innerHTML = `
            <div class="card-header" style="background: #fafdfa; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; color: var(--primary); font-size: 14px;">
                    Question <span class="q-num-label">${document.querySelectorAll('.question-card').length + 1}</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-q-btn" title="Remove question">
                    ✕ Remove
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Question Statement <span class="required">*</span></label>
                    <textarea 
                        name="questions[${index}][question_text]" 
                        rows="2" 
                        class="form-control q-text-input" 
                        placeholder="Enter question text here..." 
                        required
                    >${text}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(A)</strong> Option A <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="questions[${index}][option_a]" 
                                class="form-control" 
                                value="${optA}" 
                                placeholder="Option A text" 
                                required
                            >
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(B)</strong> Option B <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="questions[${index}][option_b]" 
                                class="form-control" 
                                value="${optB}" 
                                placeholder="Option B text" 
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(C)</strong> Option C <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="questions[${index}][option_c]" 
                                class="form-control" 
                                value="${optC}" 
                                placeholder="Option C text" 
                                required
                            >
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(D)</strong> Option D <span class="required">*</span></label>
                            <input 
                                type="text" 
                                name="questions[${index}][option_d]" 
                                class="form-control" 
                                value="${optD}" 
                                placeholder="Option D text" 
                                required
                            >
                        </div>
                    </div>
                </div>

                <div style="background: #f4fbf5; border: 1px solid var(--accent-border); padding: 12px 16px; border-radius: var(--radius-sm); margin-top: 6px;">
                    <label class="form-label" style="color: var(--primary); margin-bottom: 8px;">
                        <strong>Correct Answer Key:</strong> <span class="required">*</span>
                    </label>
                    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${index}][correct_option]" value="A" ${correct === 'A' ? 'checked' : ''} style="accent-color: var(--primary);" required>
                            Option A
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${index}][correct_option]" value="B" ${correct === 'B' ? 'checked' : ''} style="accent-color: var(--primary);">
                            Option B
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${index}][correct_option]" value="C" ${correct === 'C' ? 'checked' : ''} style="accent-color: var(--primary);">
                            Option C
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${index}][correct_option]" value="D" ${correct === 'D' ? 'checked' : ''} style="accent-color: var(--primary);">
                            Option D
                        </label>
                    </div>
                </div>
            </div>
        `;

        div.querySelector('.remove-q-btn').addEventListener('click', function () {
            if (document.querySelectorAll('.question-card').length <= 1) {
                alert('An examination must have at least one question.');
                return;
            }
            div.remove();
            renumberQuestions();
            updateLiveSummary();
        });

        container.appendChild(div);
        questionIndex++;
        renumberQuestions();
        updateLiveSummary();
    }

    function renumberQuestions() {
        const cards = document.querySelectorAll('.question-card');
        cards.forEach((card, idx) => {
            const label = card.querySelector('.q-num-label');
            if (label) {
                label.textContent = idx + 1;
            }
        });
        counterDisplay.textContent = cards.length;
    }

    function updateLiveSummary() {
        const count = document.querySelectorAll('.question-card').length;
        const marksPerQ = parseInt(marksInput.value, 10) || 1;
        const duration = parseInt(durationInput.value, 10) || 0;

        summaryQCount.textContent = count;
        summaryMarksPerQ.textContent = marksPerQ;
        summaryMaxMarks.textContent = count * marksPerQ;
        summaryDuration.textContent = duration + ' mins';
    }

    marksInput.addEventListener('input', updateLiveSummary);
    durationInput.addEventListener('input', updateLiveSummary);

    document.getElementById('addQuestionBtn').addEventListener('click', function () {
        createQuestionBlock(questionIndex);
    });

    document.getElementById('addQuestionFooterBtn').addEventListener('click', function () {
        createQuestionBlock(questionIndex);
    });

    // Seed 2 initial question blocks by default
    createQuestionBlock(questionIndex, '', '', '', '', '', 'A');
    createQuestionBlock(questionIndex, '', '', '', '', '', 'B');
});
</script>
@endpush
