<?php

namespace App\Actions;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class EnrollUserAction
{
    /**
     * @param User $user
     * @param Course $course
     * @return Enrollment
     * @throws Exception
     */
    public function execute(User $user, Course $course): Enrollment
    {
        // 1. Validation: Course must be published
        if (!$course->is_published) {
            throw new Exception("Cannot enroll in an unpublished course.");
        }

        // 2. Concurrency handling: Use a transaction and lock
        return DB::transaction(function () use ($user, $course) {
            // Check if already enrolled (idempotency)
            /** @var Enrollment|null $existing */
            $existing = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->lockForUpdate() // Lock the rows to prevent race conditions
                ->first();

            if ($existing) {
                return $existing;
            }

            // Create enrollment
            return Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);
        });
    }
}
