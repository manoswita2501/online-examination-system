@extends('layouts.student')

@section('title', 'Exam: ' . $exam->title)
@section('distraction_free', true)

@section('content')
<div class="exam-screen">
    <!-- Top Examination Bar -->
    <div class="exam-header">
        <div class="exam-meta">
            <h1>{{ $exam->title }}</h1>
            <p>{{ $exam->subject }} • Candidate: <strong>{{ $student->name }}</strong> ({{ $student->student_id }})</p>
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <!-- Countdown Timer -->
            <div class="exam-timer-box" id="timerBox">
                <span style="font-size: 18px;">⏱️</span>
                <div>
                    <div class="timer-label">Time Remaining</div>
                    <div class="timer-display" id="timerDisplay">--:--</div>
                </div>
            </div>

            <!-- Submit Exam Button -->
            <button type="button" id="submitExamTopBtn" class="btn btn-accent btn-lg">
                <span>Finish & Submit Exam</span>
            </button>
        </div>
    </div>

    <!-- Exam Interactive Grid -->
    <div class="exam-grid">
        <!-- Main Question Area -->
        <main class="question-panel" id="questionPanel">
            <div class="question-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="q-number-badge" id="currentQBadge">Question 1 of {{ $questions->count() }}</span>
                    <span style="font-size: 12px; color: var(--text-muted);">({{ $exam->marks_per_question }} Marks)</span>
                </div>

                <div class="save-status-indicator" id="saveStatusIndicator">
                    <span id="saveStatusDot">●</span>
                    <span id="saveStatusText">Unanswered</span>
                </div>
            </div>

            <!-- Question Statement -->
            <div class="question-body" id="questionText">
                Loading question...
            </div>

            <!-- Options Radio List -->
            <div class="options-list" id="optionsList">
                <label class="option-item" id="optLabelA">
                    <input type="radio" name="current_option" value="A" class="option-radio" id="radioA">
                    <div class="option-text">
                        <span class="option-letter">(A)</span>
                        <span id="optTextA"></span>
                    </div>
                </label>

                <label class="option-item" id="optLabelB">
                    <input type="radio" name="current_option" value="B" class="option-radio" id="radioB">
                    <div class="option-text">
                        <span class="option-letter">(B)</span>
                        <span id="optTextB"></span>
                    </div>
                </label>

                <label class="option-item" id="optLabelC">
                    <input type="radio" name="current_option" value="C" class="option-radio" id="radioC">
                    <div class="option-text">
                        <span class="option-letter">(C)</span>
                        <span id="optTextC"></span>
                    </div>
                </label>

                <label class="option-item" id="optLabelD">
                    <input type="radio" name="current_option" value="D" class="option-radio" id="radioD">
                    <div class="option-text">
                        <span class="option-letter">(D)</span>
                        <span id="optTextD"></span>
                    </div>
                </label>
            </div>

            <!-- Action Controls -->
            <div class="question-actions">
                <div class="nav-buttons-left">
                    <button type="button" id="prevBtn" class="btn btn-secondary">
                        ← Previous
                    </button>
                    <button type="button" id="nextBtn" class="btn btn-secondary">
                        Next →
                    </button>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" id="clearSelectionBtn" class="btn btn-sm btn-outline" style="color: var(--text-muted); border-color: var(--border);">
                        Clear Selection
                    </button>
                    <button type="button" id="saveAnswerBtn" class="btn btn-primary btn-lg">
                        <span>💾 Save Answer</span>
                    </button>
                </div>
            </div>
        </main>

        <!-- Question Palette Side -->
        <aside class="palette-card">
            <div class="palette-title">Question Palette</div>
            <div class="palette-grid" id="paletteGrid">
                @foreach ($questions as $idx => $q)
                    <button 
                        type="button" 
                        class="palette-btn {{ isset($savedAnswers[$q->id]) ? 'saved' : 'unanswered' }}" 
                        data-index="{{ $idx }}"
                        data-id="{{ $q->id }}"
                        id="palBtn-{{ $idx }}"
                        title="Question {{ $idx + 1 }}"
                    >
                        {{ $idx + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="palette-legend">
                <div class="legend-item">
                    <div class="legend-swatch swatch-saved"></div>
                    <span>Answered & Saved to Database</span>
                </div>
                <div class="legend-item">
                    <div class="legend-swatch swatch-unanswered"></div>
                    <span>Unanswered Question</span>
                </div>
                <div class="legend-item">
                    <div class="legend-swatch swatch-current"></div>
                    <span>Currently Viewing</span>
                </div>
            </div>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-light);">
                <div style="font-size: 13px; margin-bottom: 12px; display: flex; justify-content: space-between;">
                    <span>Saved Answers:</span>
                    <strong id="savedCountLabel" style="color: var(--success);">0</strong> / {{ $questions->count() }}
                </div>
                <button type="button" id="submitExamSidebarBtn" class="btn btn-accent" style="width: 100%;">
                    Submit Final Examination
                </button>
            </div>
        </aside>
    </div>
</div>

<!-- Final Submission Confirmation Modal -->
<div id="confirmSubmitModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; width: 100%; max-width: 480px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: hidden;">
        <div style="background: var(--primary); color: #ffffff; padding: 18px 24px;">
            <h3 style="font-size: 17px; font-weight: 700;">Confirm Examination Submission</h3>
        </div>
        <div style="padding: 24px;">
            <p style="font-size: 14px; color: var(--text-primary); margin-bottom: 16px;">
                Are you sure you want to finish and submit your examination? Once submitted, your answers will be evaluated immediately and you cannot reopen this exam.
            </p>

            <div style="background: #f8faf8; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 14px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                    <span>Total Questions:</span>
                    <strong>{{ $questions->count() }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: var(--success);">
                    <span>Answered & Saved:</span>
                    <strong id="modalSavedCount">0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--warning);">
                    <span>Unanswered Questions:</span>
                    <strong id="modalUnsavedCount">0</strong>
                </div>
            </div>

            <form id="finalSubmitForm" action="{{ route('student.attempts.submit', $attempt) }}" method="POST">
                @csrf
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" id="modalCancelBtn" class="btn btn-secondary">
                        Return to Exam
                    </button>
                    <button type="submit" id="modalConfirmBtn" class="btn btn-accent btn-lg">
                        Yes, Submit My Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Data payload from server (Note: correct_option is NEVER included for security)
    const questions = @json($questions);
    const totalQuestions = questions.length;
    let savedAnswers = @json($savedAnswers); // { question_id: 'A' }
    let clientSelections = Object.assign({}, savedAnswers); // Tracks currently selected option in UI
    let currentIndex = 0;
    let remainingSeconds = {{ $remainingSeconds }};
    const saveAnswerUrl = "{{ route('student.attempts.answers', $attempt) }}";
    const submitUrl = "{{ route('student.attempts.submit', $attempt) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // DOM Elements
    const qBadge = document.getElementById('currentQBadge');
    const qText = document.getElementById('questionText');
    const optTextA = document.getElementById('optTextA');
    const optTextB = document.getElementById('optTextB');
    const optTextC = document.getElementById('optTextC');
    const optTextD = document.getElementById('optTextD');
    const radioA = document.getElementById('radioA');
    const radioB = document.getElementById('radioB');
    const radioC = document.getElementById('radioC');
    const radioD = document.getElementById('radioD');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const saveBtn = document.getElementById('saveAnswerBtn');
    const clearBtn = document.getElementById('clearSelectionBtn');
    const saveStatusText = document.getElementById('saveStatusText');
    const saveStatusDot = document.getElementById('saveStatusDot');
    const timerDisplay = document.getElementById('timerDisplay');
    const timerBox = document.getElementById('timerBox');
    const savedCountLabel = document.getElementById('savedCountLabel');
    const modal = document.getElementById('confirmSubmitModal');
    const modalSavedCount = document.getElementById('modalSavedCount');
    const modalUnsavedCount = document.getElementById('modalUnsavedCount');

    // 2. Load Question into View
    function renderQuestion(index) {
        if (index < 0 || index >= totalQuestions) return;
        currentIndex = index;

        const q = questions[currentIndex];
        qBadge.textContent = `Question ${currentIndex + 1} of ${totalQuestions}`;
        qText.textContent = q.question_text;
        optTextA.textContent = q.option_a;
        optTextB.textContent = q.option_b;
        optTextC.textContent = q.option_c;
        optTextD.textContent = q.option_d;

        // Check if an option was chosen
        const chosen = clientSelections[q.id] || null;
        [radioA, radioB, radioC, radioD].forEach(r => {
            r.checked = (r.value === chosen);
            updateOptionStyle(r);
        });

        updateSaveStatusIndicator();
        updatePaletteCurrentHighlight();

        // Prev / Next button state
        prevBtn.disabled = (currentIndex === 0);
        nextBtn.disabled = (currentIndex === totalQuestions - 1);
    }

    function updateOptionStyle(radio) {
        const label = radio.closest('.option-item');
        if (radio.checked) {
            label.classList.add('selected');
        } else {
            label.classList.remove('selected');
        }
    }

    // Radio change listener
    document.querySelectorAll('.option-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.option-radio').forEach(updateOptionStyle);
            const q = questions[currentIndex];
            clientSelections[q.id] = this.value;
            updateSaveStatusIndicator();
        });
    });

    // Clear selection
    clearBtn.addEventListener('click', function () {
        const q = questions[currentIndex];
        delete clientSelections[q.id];
        [radioA, radioB, radioC, radioD].forEach(r => {
            r.checked = false;
            updateOptionStyle(r);
        });
        updateSaveStatusIndicator();
    });

    // Status indicator
    function updateSaveStatusIndicator() {
        const q = questions[currentIndex];
        const saved = savedAnswers[q.id] || null;
        const current = clientSelections[q.id] || null;

        if (!current && !saved) {
            saveStatusText.textContent = 'Unanswered';
            saveStatusText.style.color = 'var(--text-muted)';
            saveStatusDot.style.color = 'var(--text-muted)';
        } else if (saved && saved === current) {
            saveStatusText.textContent = `Answer (${saved}) Saved to Database ✓`;
            saveStatusText.style.color = 'var(--success)';
            saveStatusDot.style.color = 'var(--success)';
        } else if (current && current !== saved) {
            saveStatusText.textContent = `Selected (${current}) - Click 'Save Answer' to persist`;
            saveStatusText.style.color = 'var(--warning)';
            saveStatusDot.style.color = 'var(--warning)';
        }
    }

    function updatePaletteCurrentHighlight() {
        document.querySelectorAll('.palette-btn').forEach((btn, idx) => {
            if (idx === currentIndex) {
                btn.classList.add('current');
            } else {
                btn.classList.remove('current');
            }
        });
    }

    function refreshPaletteStates() {
        let count = 0;
        questions.forEach((q, idx) => {
            const btn = document.getElementById(`palBtn-${idx}`);
            if (savedAnswers[q.id]) {
                btn.classList.remove('unanswered');
                btn.classList.add('saved');
                count++;
            } else {
                btn.classList.remove('saved');
                btn.classList.add('unanswered');
            }
        });
        savedCountLabel.textContent = count;
    }

    // 3. Save Answer via Fetch Request
    async function saveCurrentAnswer() {
        const q = questions[currentIndex];
        const selected = clientSelections[q.id] || null;

        if (!selected) {
            alert('Please select an option (A, B, C, or D) before clicking Save Answer.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const response = await fetch(saveAnswerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    question_id: q.id,
                    selected_option: selected
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                savedAnswers[q.id] = data.selected_option;
                clientSelections[q.id] = data.selected_option;
                refreshPaletteStates();
                updateSaveStatusIndicator();
                saveBtn.textContent = '✓ Saved';
                setTimeout(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = '💾 Save Answer';
                }, 900);
            } else if (data.expired && data.redirect) {
                alert('Time expired. Submitting examination now.');
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Error saving answer. Please try again.');
                saveBtn.disabled = false;
                saveBtn.textContent = '💾 Save Answer';
            }
        } catch (err) {
            console.error(err);
            alert('Network error while saving answer. Please check your connection and try again.');
            saveBtn.disabled = false;
            saveBtn.textContent = '💾 Save Answer';
        }
    }

    saveBtn.addEventListener('click', saveCurrentAnswer);

    // 4. Navigation with Unsaved Check
    function checkUnsavedBeforeNavigate(targetIndex) {
        const q = questions[currentIndex];
        const saved = savedAnswers[q.id] || null;
        const current = clientSelections[q.id] || null;

        if (current && current !== saved) {
            const leave = confirm(`You have selected Option (${current}) for Question ${currentIndex + 1} but have NOT saved it yet.\n\nClick Cancel to stay and click 'Save Answer', or OK to navigate without saving.`);
            if (!leave) return false;
            // If user discarded change, revert clientSelection to saved
            if (saved) {
                clientSelections[q.id] = saved;
            } else {
                delete clientSelections[q.id];
            }
        }
        renderQuestion(targetIndex);
        return true;
    }

    prevBtn.addEventListener('click', function () {
        if (currentIndex > 0) checkUnsavedBeforeNavigate(currentIndex - 1);
    });

    nextBtn.addEventListener('click', function () {
        if (currentIndex < totalQuestions - 1) checkUnsavedBeforeNavigate(currentIndex + 1);
    });

    document.querySelectorAll('.palette-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.dataset.index, 10);
            if (idx !== currentIndex) {
                checkUnsavedBeforeNavigate(idx);
            }
        });
    });

    // 5. Countdown Timer
    function formatTime(totalSecs) {
        const mins = Math.floor(totalSecs / 60);
        const secs = totalSecs % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    function updateTimer() {
        if (remainingSeconds <= 0) {
            timerDisplay.textContent = '00:00';
            timerBox.classList.add('timer-warning');
            clearInterval(timerInterval);
            autoSubmitOnTimeout();
            return;
        }

        timerDisplay.textContent = formatTime(remainingSeconds);

        // Warning when <= 5 minutes (300s)
        if (remainingSeconds <= 300) {
            timerBox.classList.add('timer-warning');
        }

        remainingSeconds--;
    }

    async function autoSubmitOnTimeout() {
        alert('Examination time has expired! Your responses will now be submitted automatically.');
        document.getElementById('finalSubmitForm').submit();
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Initial render

    // 6. Confirmation Modal for Final Submission
    function openSubmitModal() {
        const savedCount = Object.keys(savedAnswers).length;
        const unsavedCount = totalQuestions - savedCount;

        modalSavedCount.textContent = savedCount;
        modalUnsavedCount.textContent = unsavedCount;
        modal.style.display = 'flex';
    }

    document.getElementById('submitExamTopBtn').addEventListener('click', openSubmitModal);
    document.getElementById('submitExamSidebarBtn').addEventListener('click', openSubmitModal);

    document.getElementById('modalCancelBtn').addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Initial render
    refreshPaletteStates();
    renderQuestion(0);
});
</script>
@endpush
