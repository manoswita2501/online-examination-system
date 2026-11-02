<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->restrictOnDelete();
            $table->string('title');
            $table->string('subject');
            $table->text('instructions')->nullable();
            $table->date('exam_date');
            $table->time('start_time');
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('marks_per_question')->default(1);
            $table->string('status', 20)->default('draft'); // draft, published, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
