<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OnlineExamSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test home page loads.
     */
    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Student Examination Portal');
        $response->assertSee('Administrator Control Center');
    }

    /**
     * Test student authentication with valid credentials.
     */
    public function test_student_can_login_with_valid_credentials(): void
    {
        $batch = Batch::firstOrCreate([
            'course_name' => 'BTech Test',
            'department' => 'Test Dept',
            'semester' => 'Sem 1',
            'section' => 'A',
            'academic_year' => '2025-2026',
        ]);

        $student = User::firstOrCreate(
            ['email' => 'teststudent@example.com'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => 'STU_TEST_01',
                'batch_id' => $batch->id,
                'is_active' => true,
            ]
        );

        $response = $this->post(route('student.login.submit'), [
            'email' => 'teststudent@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    /**
     * Test student login fails with invalid credentials.
     */
    public function test_student_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post(route('student.login.submit'), [
            'email' => 'teststudent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test admin authentication with 5-digit Admin ID.
     */
    public function test_admin_can_login_with_valid_5_digit_id(): void
    {
        $admin = User::firstOrCreate(
            ['admin_id' => '01234'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $response = $this->post(route('admin.login.submit'), [
            'admin_id' => '01234',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test admin login rejects non 5-digit ID.
     */
    public function test_admin_login_rejects_invalid_admin_id_format(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'admin_id' => '123', // not 5 digits
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('admin_id');
        $this->assertGuest();
    }

    /**
     * Test role-based protection: Student cannot access Admin Dashboard.
     */
    public function test_student_cannot_access_admin_routes(): void
    {
        $student = User::where('role', 'student')->first();
        if (!$student) {
            $student = User::create([
                'name' => 'Stud',
                'email' => 'stud@exam.edu',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($student)->get(route('admin.dashboard'));
        // Should redirect back to student dashboard with error
        $response->assertRedirect(route('student.dashboard'));
    }

    /**
     * Test batch restriction: Student cannot access exam of another batch.
     */
    public function test_all_students_can_access_published_exams_in_real_time(): void
    {
        $batchA = Batch::create([
            'course_name' => 'Course A',
            'department' => 'Dept A',
            'semester' => 'Sem 1',
            'section' => 'A',
            'academic_year' => '2025-2026',
        ]);

        $batchB = Batch::create([
            'course_name' => 'Course B',
            'department' => 'Dept B',
            'semester' => 'Sem 1',
            'section' => 'B',
            'academic_year' => '2025-2026',
        ]);

        $studentA = User::create([
            'name' => 'Student A',
            'email' => 'studentA@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => 'STU_A',
            'batch_id' => $batchA->id,
            'is_active' => true,
        ]);

        $examB = Exam::create([
            'title' => 'Exam for Batch B only',
            'subject' => 'Subject B',
            'batch_id' => $batchB->id,
            'exam_date' => now()->toDateString(),
            'start_time' => '00:00:00',
            'duration_minutes' => 60,
            'marks_per_question' => 2,
            'status' => 'published',
        ]);

        // Student A can access instructions and start Exam B in real time
        $response = $this->actingAs($studentA)->get(route('student.exams.instructions', $examB));
        $response->assertStatus(200);

        $startResponse = $this->actingAs($studentA)->post(route('student.exams.start', $examB));
        $startResponse->assertRedirect();
    }

    /**
     * Test examination workflow: start attempt, explicitly save answer, submit, and verify score calculation.
     */
    public function test_student_exam_workflow_saving_answers_and_scoring(): void
    {
        $batch = Batch::create([
            'course_name' => 'CSE Test',
            'department' => 'CSE',
            'semester' => 'Sem 2',
            'section' => 'C',
            'academic_year' => '2025-2026',
        ]);

        $student = User::create([
            'name' => 'Exam Taker',
            'email' => 'examtaker@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => 'STU_TAKER_01',
            'batch_id' => $batch->id,
            'is_active' => true,
        ]);

        $exam = Exam::create([
            'title' => 'Test MCQ Exam',
            'subject' => 'Testing 101',
            'batch_id' => $batch->id,
            'exam_date' => now()->toDateString(),
            'start_time' => '00:00:00',
            'duration_minutes' => 45,
            'marks_per_question' => 5,
            'status' => 'published',
        ]);

        $q1 = Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'What is 2 + 2?',
            'option_a' => '3',
            'option_b' => '4',
            'option_c' => '5',
            'option_d' => '6',
            'correct_option' => 'B',
        ]);

        $q2 = Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'What is the capital of France?',
            'option_a' => 'London',
            'option_b' => 'Berlin',
            'option_c' => 'Paris',
            'option_d' => 'Madrid',
            'correct_option' => 'C',
        ]);

        // 1. Start exam
        $response = $this->actingAs($student)->post(route('student.exams.start', $exam));
        $attempt = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->first();
        $this->assertNotNull($attempt);
        $response->assertRedirect(route('student.attempts.show', $attempt));

        // 2. Explicitly save answer for Q1 (Correct: Option B)
        $saveResp1 = $this->actingAs($student)->postJson(route('student.attempts.answers', $attempt), [
            'question_id' => $q1->id,
            'selected_option' => 'B',
        ]);
        $saveResp1->assertStatus(200);
        $saveResp1->assertJson(['success' => true, 'selected_option' => 'B']);

        // Verify saved in DB
        $this->assertDatabaseHas('student_answers', [
            'exam_attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'selected_option' => 'B',
        ]);

        // 3. Explicitly save answer for Q2 (Incorrect: Option A instead of C)
        $saveResp2 = $this->actingAs($student)->postJson(route('student.attempts.answers', $attempt), [
            'question_id' => $q2->id,
            'selected_option' => 'A',
        ]);
        $saveResp2->assertStatus(200);

        // 4. Submit Exam
        $submitResp = $this->actingAs($student)->post(route('student.attempts.submit', $attempt));
        $submitResp->assertRedirect(route('student.results.show', $attempt));

        // 5. Verify final calculated scores
        $attempt->refresh();
        $this->assertEquals('submitted', $attempt->status);
        $this->assertEquals(2, $attempt->total_questions);
        $this->assertEquals(2, $attempt->answered_count);
        $this->assertEquals(0, $attempt->unanswered_count);
        $this->assertEquals(1, $attempt->correct_count);
        $this->assertEquals(1, $attempt->incorrect_count);
        $this->assertEquals(5.00, $attempt->score); // 1 correct * 5 marks
        $this->assertEquals(50.00, $attempt->percentage); // 5 / 10 = 50%
    }

    /**
     * Test scheduled command finalizes expired attempts automatically.
     */
    public function test_expired_attempts_are_finalized_by_scheduled_artisan_command(): void
    {
        $batch = Batch::create([
            'course_name' => 'Auto Batch',
            'department' => 'CSE',
            'semester' => 'Sem 3',
            'section' => 'A',
            'academic_year' => '2025-2026',
        ]);

        $student = User::create([
            'name' => 'Auto Student',
            'email' => 'autostudent@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => 'STU_AUTO_01',
            'batch_id' => $batch->id,
            'is_active' => true,
        ]);

        $exam = Exam::create([
            'title' => 'Expired Test Exam',
            'subject' => 'System Auto Finalize',
            'batch_id' => $batch->id,
            'exam_date' => now()->toDateString(),
            'start_time' => '00:00:00',
            'duration_minutes' => 30,
            'marks_per_question' => 2,
            'status' => 'published',
        ]);

        $q = Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Sample Q?',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        // Attempt started 50 minutes ago for a 30-minute exam
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => now()->subMinutes(50),
            'status' => 'in_progress',
            'total_questions' => 1,
        ]);

        // Save a correct answer
        StudentAnswer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $q->id,
            'selected_option' => 'A',
        ]);

        $this->assertTrue($attempt->hasExpired());

        // Run the artisan command
        $this->artisan('exam:finalize-expired')->assertExitCode(0);

        // Verify status is auto_submitted and scored
        $attempt->refresh();
        $this->assertEquals('auto_submitted', $attempt->status);
        $this->assertEquals(2.00, $attempt->score);
        $this->assertEquals(1, $attempt->correct_count);
    }

    /**
     * Test batch cannot be deleted if active students exist.
     */
    public function test_batch_cannot_be_deleted_if_students_exist(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'adminboss@example.com',
            'admin_id' => '99999',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_name' => 'Protected Batch',
            'department' => 'Dept',
            'semester' => 'Sem 1',
            'section' => 'A',
            'academic_year' => '2025-2026',
        ]);

        User::create([
            'name' => 'Enrolled Kid',
            'email' => 'kid@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => 'STU_KID_01',
            'batch_id' => $batch->id,
            'is_active' => true,
        ]);

        // Delete request from admin
        $response = $this->actingAs($admin)->delete(route('admin.batches.destroy', $batch));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('batches', ['id' => $batch->id]);
    }

    /**
     * Test exam cannot be edited after an attempt has begun.
     */
    public function test_exam_cannot_be_edited_after_attempt_begins(): void
    {
        $admin = User::create([
            'name' => 'Admin Exam Mgr',
            'email' => 'exammgr@example.com',
            'admin_id' => '88888',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_name' => 'Lock Batch',
            'department' => 'Dept',
            'semester' => 'Sem 1',
            'section' => 'A',
            'academic_year' => '2025-2026',
        ]);

        $exam = Exam::create([
            'title' => 'Locked Exam',
            'subject' => 'Lock Test',
            'batch_id' => $batch->id,
            'exam_date' => now()->toDateString(),
            'start_time' => '00:00:00',
            'duration_minutes' => 30,
            'marks_per_question' => 2,
            'status' => 'published',
        ]);

        $student = User::create([
            'name' => 'Active Candidate',
            'email' => 'activecand@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => 'STU_LOCK_01',
            'batch_id' => $batch->id,
            'is_active' => true,
        ]);

        // Student starts attempt
        ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        // Admin attempts to visit edit page
        $editResp = $this->actingAs($admin)->get(route('admin.exams.edit', $exam));
        $editResp->assertRedirect(route('admin.exams.show', $exam));
        $editResp->assertSessionHas('error');
    }

    /**
     * Test admin can view student report for printing.
     */
    public function test_admin_can_view_student_report_for_printing(): void
    {
        $admin = User::create([
            'name' => 'Admin Report Viewer',
            'email' => 'reportadmin@example.com',
            'admin_id' => '77777',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_name' => 'Report Batch',
            'department' => 'CS Dept',
            'semester' => 'Sem 1',
            'section' => 'A',
            'academic_year' => '2025-2026',
        ]);

        $student = User::create([
            'name' => 'Report Student',
            'email' => 'reportstudent@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => 'STU_RPT_01',
            'batch_id' => $batch->id,
            'is_active' => true,
        ]);

        $exam = Exam::create([
            'title' => 'Report Test Exam',
            'subject' => 'Operating Systems',
            'batch_id' => $batch->id,
            'exam_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'duration_minutes' => 60,
            'marks_per_question' => 2,
            'status' => 'published',
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => now()->subMinutes(30),
            'submitted_at' => now(),
            'status' => 'submitted',
            'score' => 2,
            'max_score' => 2,
            'percentage' => 100,
            'total_questions' => 1,
            'answered_count' => 1,
            'unanswered_count' => 0,
            'correct_count' => 1,
            'incorrect_count' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.student', $attempt));
        $response->assertStatus(200);
        $response->assertSee('Office of the Controller of Examinations');
        $response->assertSee('Administrator Evaluation Sheet');
        $response->assertSee('print-only-header');
        $response->assertSee('print-only-footer');
        $response->assertSee('Report Student');
    }

    /**
     * Test admin can view consolidated report for printing.
     */
    public function test_admin_can_view_consolidated_report_for_printing(): void
    {
        $admin = User::create([
            'name' => 'Admin Cons Viewer',
            'email' => 'consadmin@example.com',
            'admin_id' => '66666',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_name' => 'Cons Batch',
            'department' => 'IT Dept',
            'semester' => 'Sem 2',
            'section' => 'B',
            'academic_year' => '2025-2026',
        ]);

        $exam = Exam::create([
            'title' => 'Batch Midterm Exam',
            'subject' => 'Data Structures',
            'batch_id' => $batch->id,
            'exam_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'duration_minutes' => 45,
            'marks_per_question' => 2,
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.consolidated', $exam));
        $response->assertStatus(200);
        $response->assertSee('Office of the Controller of Examinations');
        $response->assertSee('Official Consolidated Batch Result');
        $response->assertSee('print-only-header');
        $response->assertSee('print-only-footer');
    }
}

