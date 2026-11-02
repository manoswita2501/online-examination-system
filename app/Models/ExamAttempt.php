<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'status',
        'score',
        'total_questions',
        'answered_count',
        'unanswered_count',
        'correct_count',
        'incorrect_count',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
            'total_questions' => 'integer',
            'answered_count' => 'integer',
            'unanswered_count' => 'integer',
            'correct_count' => 'integer',
            'incorrect_count' => 'integer',
        ];
    }

    /**
     * Relationship: Attempt belongs to a student (User).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relationship: Attempt belongs to an Exam.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    /**
     * Relationship: Attempt has many StudentAnswers.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'exam_attempt_id');
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'auto_submitted']);
    }

    /**
     * Calculate deadline based on session started_at + exam duration minutes.
     */
    public function getDeadlineAttribute(): Carbon
    {
        return $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
    }

    /**
     * Check if the attempt has expired.
     */
    public function hasExpired(): bool
    {
        return now()->greaterThan($this->deadline);
    }

    /**
     * Seconds remaining for the attempt.
     */
    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->isInProgress()) {
            return 0;
        }

        $now = now();
        $deadline = $this->deadline;

        if ($now->greaterThanOrEqualTo($deadline)) {
            return 0;
        }

        return $now->diffInSeconds($deadline);
    }

    /**
     * Maximum possible score for this exam.
     */
    public function getMaxScoreAttribute(): float|int
    {
        $count = $this->total_questions > 0 ? $this->total_questions : ($this->exam ? $this->exam->questions()->count() : 0);
        $marksPerQ = $this->exam ? $this->exam->marks_per_question : 1;
        return $count * $marksPerQ;
    }

    /**
     * Calculate percentage safely.
     */
    public function getPercentageAttribute(): float
    {
        $max = $this->max_score;
        if ($max <= 0) {
            return 0.0;
        }
        return round(($this->score / $max) * 100, 2);
    }

    /**
     * Formatted time taken.
     */
    public function getTimeTakenFormattedAttribute(): string
    {
        if (!$this->started_at) {
            return 'N/A';
        }

        $end = $this->submitted_at ?? now();
        $diffMinutes = $this->started_at->diffInMinutes($end);
        $diffSeconds = $this->started_at->diffInSeconds($end) % 60;

        return "{$diffMinutes} min {$diffSeconds} sec";
    }
}
