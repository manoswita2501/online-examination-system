<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_name',
        'department',
        'semester',
        'section',
        'academic_year',
    ];

    /**
     * Relationship: Batch has many students.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'batch_id')->where('role', 'student');
    }

    /**
     * Relationship: Batch has many examinations.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'batch_id');
    }

    /**
     * Accessor for a friendly full name label.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->course_name} - {$this->semester} ({$this->section}, {$this->academic_year})";
    }
}
