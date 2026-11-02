@extends('layouts.admin')

@section('title', 'Edit Examination')
@section('page_title', 'Edit Examination')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline">← Back to Exam Details</a>
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

<form action="{{ route('admin.exams.update', $exam) }}" method="POST" id="examForm">
    @csrf
    @method('PUT')

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
                            value="{{ old('title', $exam->title) }}" 
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
                            value="{{ old('subject', $exam->subject) }}" 
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
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}" {{ (old('batch_id', $exam->batch_id) == $batch->id) ? 'selected' : '' }}>
                                    {{ $batch->course_name }} - {{ $batch->semester }} ({{ $batch->section }}, {{ $batch->academic_year }})
                                </option>
                            @endforeach
                        </select>
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
                            value="{{ old('marks_per_question', $exam->marks_per_question) }}" 
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
                            value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" 
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
                            value="{{ old('start_time', substr($exam->start_time, 0, 5)) }}" 
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
                            value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
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
                >{{ old('instructions', $exam->instructions) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Publication Status <span class="required">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="draft" {{ old('status', $exam->status) === 'draft' ? 'selected' : '' }}>Draft (Hidden from students)</option>
                    <option value="published" {{ old('status', $exam->status) === 'published' ? 'selected' : '' }}>Published (Visible to batch students)</option>
                    <option value="closed" {{ old('status', $exam->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SECTION B: Questions Entry -->
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
            @foreach ($exam->questions as $idx => $q)
                <div class="card question-card" style="margin-bottom: 20px; border: 1.5px solid var(--border);" data-index="{{ $idx }}">
                    <div class="card-header" style="background: #fafdfa; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-weight: 700; color: var(--primary); font-size: 14px;">
                            Question <span class="q-num-label">{{ $idx + 1 }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-q-btn">✕ Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Question Statement <span class="required">*</span></label>
                            <textarea 
                                name="questions[{{ $idx }}][question_text]" 
                                rows="2" 
                                class="form-control" 
                                required
                            >{{ old("questions.{$idx}.question_text", $q->question_text) }}</textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label class="form-label"><strong>(A)</strong> Option A <span class="required">*</span></label>
                                    <input 
                                        type="text" 
                                        name="questions[{{ $idx }}][option_a]" 
                                        class="form-control" 
                                        value="{{ old("questions.{$idx}.option_a", $q->option_a) }}" 
                                        required
                                    >
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label class="form-label"><strong>(B)</strong> Option B <span class="required">*</span></label>
                                    <input 
                                        type="text" 
                                        name="questions[{{ $idx }}][option_b]" 
                                        class="form-control" 
                                        value="{{ old("questions.{$idx}.option_b", $q->option_b) }}" 
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
                                        name="questions[{{ $idx }}][option_c]" 
                                        class="form-control" 
                                        value="{{ old("questions.{$idx}.option_c", $q->option_c) }}" 
                                        required
                                    >
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label class="form-label"><strong>(D)</strong> Option D <span class="required">*</span></label>
                                    <input 
                                        type="text" 
                                        name="questions[{{ $idx }}][option_d]" 
                                        class="form-control" 
                                        value="{{ old("questions.{$idx}.option_d", $q->option_d) }}" 
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
                                @foreach (['A', 'B', 'C', 'D'] as $opt)
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                                        <input 
                                            type="radio" 
                                            name="questions[{{ $idx }}][correct_option]" 
                                            value="{{ $opt }}" 
                                            {{ old("questions.{$idx}.correct_option", $q->correct_option) === $opt ? 'checked' : '' }} 
                                            style="accent-color: var(--primary);" 
                                            required
                                        >
                                        Option {{ $opt }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card-footer" style="background: #f8faf8; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 13px; color: var(--text-muted);">
                Total Questions: <strong id="questionCounter">{{ $exam->questions->count() }}</strong>
            </span>
            <button type="button" id="addQuestionFooterBtn" class="btn btn-secondary btn-sm">
                <span>➕ Add Question</span>
            </button>
        </div>
    </div>

    <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 12px;">
        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary btn-lg">Update Examination</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let questionIndex = {{ $exam->questions->count() }};
    const container = document.getElementById('questionsContainer');
    const counterDisplay = document.getElementById('questionCounter');

    function bindRemoveBtn(btn, card) {
        btn.addEventListener('click', function () {
            if (document.querySelectorAll('.question-card').length <= 1) {
                alert('An examination must have at least one question.');
                return;
            }
            card.remove();
            renumberQuestions();
        });
    }

    document.querySelectorAll('.question-card').forEach(card => {
        const btn = card.querySelector('.remove-q-btn');
        if (btn) bindRemoveBtn(btn, card);
    });

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

    function addQuestion() {
        const div = document.createElement('div');
        div.className = 'card question-card';
        div.style.marginBottom = '20px';
        div.style.border = '1.5px solid var(--border)';
        div.dataset.index = questionIndex;

        div.innerHTML = `
            <div class="card-header" style="background: #fafdfa; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; color: var(--primary); font-size: 14px;">
                    Question <span class="q-num-label">${document.querySelectorAll('.question-card').length + 1}</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-q-btn">✕ Remove</button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Question Statement <span class="required">*</span></label>
                    <textarea name="questions[${questionIndex}][question_text]" rows="2" class="form-control" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(A)</strong> Option A <span class="required">*</span></label>
                            <input type="text" name="questions[${questionIndex}][option_a]" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(B)</strong> Option B <span class="required">*</span></label>
                            <input type="text" name="questions[${questionIndex}][option_b]" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(C)</strong> Option C <span class="required">*</span></label>
                            <input type="text" name="questions[${questionIndex}][option_c]" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label"><strong>(D)</strong> Option D <span class="required">*</span></label>
                            <input type="text" name="questions[${questionIndex}][option_d]" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div style="background: #f4fbf5; border: 1px solid var(--accent-border); padding: 12px 16px; border-radius: var(--radius-sm); margin-top: 6px;">
                    <label class="form-label" style="color: var(--primary); margin-bottom: 8px;">
                        <strong>Correct Answer Key:</strong> <span class="required">*</span>
                    </label>
                    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${questionIndex}][correct_option]" value="A" checked style="accent-color: var(--primary);" required> Option A
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${questionIndex}][correct_option]" value="B" style="accent-color: var(--primary);"> Option B
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${questionIndex}][correct_option]" value="C" style="accent-color: var(--primary);"> Option C
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600;">
                            <input type="radio" name="questions[${questionIndex}][correct_option]" value="D" style="accent-color: var(--primary);"> Option D
                        </label>
                    </div>
                </div>
            </div>
        `;

        bindRemoveBtn(div.querySelector('.remove-q-btn'), div);
        container.appendChild(div);
        questionIndex++;
        renumberQuestions();
    }

    document.getElementById('addQuestionBtn').addEventListener('click', addQuestion);
    document.getElementById('addQuestionFooterBtn').addEventListener('click', addQuestion);
});
</script>
@endpush
