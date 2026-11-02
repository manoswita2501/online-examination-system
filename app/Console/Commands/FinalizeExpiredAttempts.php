<?php

namespace App\Console\Commands;

use App\Models\ExamAttempt;
use App\Services\ExamEvaluationService;
use Illuminate\Console\Command;

class FinalizeExpiredAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:finalize-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finalize and score any in-progress exam attempts whose time limit has expired';

    /**
     * Execute the console command.
     */
    public function handle(ExamEvaluationService $evaluationService): int
    {
        $inProgressAttempts = ExamAttempt::where('status', 'in_progress')->with('exam')->get();
        $finalizedCount = 0;

        foreach ($inProgressAttempts as $attempt) {
            if ($attempt->hasExpired()) {
                $evaluationService->finalizeAttempt($attempt, 'auto_submitted');
                $finalizedCount++;
                $this->info("Finalized expired attempt #{$attempt->id} for student #{$attempt->student_id} on exam #{$attempt->exam_id}.");
            }
        }

        $this->info("Expired exam finalization completed. Total finalized: {$finalizedCount}.");
        return Command::SUCCESS;
    }
}
