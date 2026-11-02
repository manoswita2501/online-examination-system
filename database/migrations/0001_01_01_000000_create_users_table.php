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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('course_name'); // e.g., BTech CSE
            $table->string('department');  // e.g., Computer Science & Engineering
            $table->string('semester');    // e.g., Semester 1
            $table->string('section');     // e.g., Section A
            $table->string('academic_year'); // e.g., 2025-2026
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('role', 20)->default('student'); // 'admin' or 'student'
            $table->string('admin_id', 10)->nullable()->unique(); // 5-digit string e.g. '01234'
            $table->string('student_id', 50)->nullable()->unique(); // e.g. 'STU2025001'
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('batches');
    }
};
