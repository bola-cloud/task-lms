<?php

namespace App\Actions;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GenerateCertificateAction
{
    /**
     * Generate a certificate for a user and course.
     * 
     * This method use a transaction and pessimistic locking to ensure
     * exactly one certificate is generated per user/course. It is idempotent.
     * 
     * @param User $user
     * @param Course $course
     * @return Certificate
     */
    public function execute(User $user, Course $course): Certificate
    {
        return DB::transaction(function () use ($user, $course) {
            /** @var Certificate|null $existing */
            $existing = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            return Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'issued_at' => now(),
            ]);
        });
    }
}
