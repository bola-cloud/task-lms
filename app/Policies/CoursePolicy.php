<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view the course content (enrolled).
     */
    public function view(User $user, Course $course): bool
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }
}
