<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\DB;

class ExamEvaluationService
{
    /**
     * Finalize an exam attempt, evaluate all answers, calculate scores, and update status.
     *
     * @param ExamAttempt $attempt
     * @param string $finalStatus 'submitted' or 'auto_submitted'
     * @return ExamAttempt
     */
    public function finalizeAttempt(ExamAttempt $attempt, string $finalStatus = 'submitted'): ExamAttempt
    {
        return DB::transaction(function () use ($attempt, $finalStatus) {
            // Lock attempt row to prevent concurrent submissions
            $attempt = ExamAttempt::where('id', $attempt->id)->lockForUpdate()->firstOrFail();

            // If already finalized, return immediately without re-grading
            if ($attempt->isSubmitted()) {
                return $attempt;
            }

            $exam = $attempt->exam()->with('questions')->firstOrFail();
            $questions = $exam->questions;
            $totalQuestions = $questions->count();
            $marksPerQuestion = $exam->marks_per_question;

            // Load all student answers for this attempt
            $answers = StudentAnswer::where('exam_attempt_id', $attempt->id)->get()->keyBy('question_id');

            $answeredCount = 0;
            $correctCount = 0;
            $incorrectCount = 0;
            $totalScore = 0.0;

            foreach ($questions as $question) {
                $studentAnswer = $answers->get($question->id);

                if ($studentAnswer && !empty($studentAnswer->selected_option)) {
                    $answeredCount++;
                    $isCorrect = (strtoupper($studentAnswer->selected_option) === strtoupper($question->correct_option));

                    if ($isCorrect) {
                        $correctCount++;
                        $marksAwarded = $marksPerQuestion;
                        $totalScore += $marksAwarded;
                    } else {
                        $incorrectCount++;
                        $marksAwarded = 0.0;
                    }

                    // Update student answer record
                    $studentAnswer->update([
                        'is_correct' => $isCorrect,
                        'marks_awarded' => $marksAwarded,
                    ]);
                }
            }

            $unansweredCount = $totalQuestions - $answeredCount;

            $attempt->update([
                'status' => in_array($finalStatus, ['submitted', 'auto_submitted']) ? $finalStatus : 'submitted',
                'submitted_at' => now(),
                'total_questions' => $totalQuestions,
                'answered_count' => $answeredCount,
                'unanswered_count' => $unansweredCount,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
                'score' => $totalScore,
            ]);

            return $attempt->fresh();
        });
    }
}
