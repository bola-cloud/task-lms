<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Events\LessonCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\LessonProgress;

class CheckCourseCompletion
{
    /**
     * Handle the event.
     */
    public function handle(LessonCompleted $event): void
    {
        $user = $event->user;
        $course = $event->lesson->course;

        // Get all required lessons for this course
        $requiredLessonIds = $course->lessons()
            ->where('is_required', true)
            ->pluck('id');

        // Get completed lessons for this user in this course
        $completedRequiredCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $requiredLessonIds)
            ->whereNotNull('completed_at')
            ->count();

        if ($completedRequiredCount === $requiredLessonIds->count()) {
            event(new CourseCompleted($user, $course));
        }
    }
}
