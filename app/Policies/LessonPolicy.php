<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine whether the user can view the lesson.
     */
    public function view(?User $user, Lesson $lesson): bool
    {
        // Guests can view free previews
        if ($lesson->is_free_preview) {
            return true;
        }

        // Authenticated users must be enrolled in the course
        if (!$user) {
            return false;
        }

        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->exists();
    }
}
