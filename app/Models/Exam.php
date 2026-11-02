<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'title',
        'subject',
        'instructions',
        'exam_date',
        'start_time',
        'duration_minutes',
        'marks_per_question',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'duration_minutes' => 'integer',
            'marks_per_question' => 'integer',
        ];
    }

    /**
     * Relationship: Exam belongs to one Batch.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    /**
     * Relationship: Exam has many Questions.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'exam_id');
    }

    /**
     * Relationship: Exam has many ExamAttempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Combine exam_date and start_time into a Carbon datetime.
     */
    public function getStartDateTimeAttribute(): Carbon
    {
        $dateStr = $this->exam_date ? $this->exam_date->format('Y-m-d') : now()->format('Y-m-d');
        $timeStr = $this->start_time ?? '00:00:00';
        return Carbon::parse("{$dateStr} {$timeStr}");
    }

    /**
     * Examination window stays active unless explicitly closed by the administrator.
     */
    public function getEndDateTimeAttribute(): Carbon
    {
        return $this->start_date_time->copy()->addYear();
    }

    /**
     * Maximum marks possible.
     */
    public function getTotalMarksAttribute(): float|int
    {
        return $this->questions()->count() * $this->marks_per_question;
    }
}
