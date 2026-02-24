<?php

namespace App\Actions;

use App\Events\LessonCompleted;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompleteLessonAction
{
    /**
     * Mark a lesson as completed for a user.
     * 
     * This method is transactional and idempotent. It tracks the start of a lesson
     * if it wasn't tracked, and sets the completion timestamp. It dispatches
     * the LessonCompleted event exactly once when the completion mark is first set.
     * 
     * @param User $user
     * @param Lesson $lesson
     * @return void
     */
    public function execute(User $user, Lesson $lesson): void
    {
        DB::transaction(function () use ($user, $lesson) {
            /** @var LessonProgress $progress */
            $progress = LessonProgress::firstOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['started_at' => now()]
            );

            if (!$progress->completed_at) {
                $progress->update(['completed_at' => now()]);

                // Dispatch event only once per lesson completion
                event(new LessonCompleted($user, $lesson));
            }
        });
    }
}
