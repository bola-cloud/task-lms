<?php

namespace Tests\Feature;

use App\Actions\CompleteLessonAction;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Certificate;
use App\Mail\CourseCompletionMail;
use Illuminate\Support\Facades\Mail;

test('completing all required lessons triggers certificate once', function () {
    Mail::fake();

    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lessons = Lesson::factory()->count(2)->create([
        'course_id' => $course->id,
        'is_required' => true
    ]);

    $action = new CompleteLessonAction();

    // Complete first lesson
    $action->execute($user, $lessons[0]);
    expect(Certificate::count())->toBe(0);

    // Complete second lesson
    $action->execute($user, $lessons[1]);

    expect(Certificate::count())->toBe(1);
    expect(Certificate::where('user_id', $user->id)->where('course_id', $course->id)->exists())->toBeTrue();

    Mail::assertSent(CourseCompletionMail::class, 1);
});

test('certificate is not issued twice if lesson completion is retried', function () {
    Mail::fake();

    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'is_required' => true
    ]);

    $action = new CompleteLessonAction();

    // First completion
    $action->execute($user, $lesson);
    expect(Certificate::count())->toBe(1);
    Mail::assertSent(CourseCompletionMail::class, 1);

    // Second completion attempt
    $action->execute($user, $lesson);
    expect(Certificate::count())->toBe(1);
    Mail::assertSent(CourseCompletionMail::class, 1); // Still only 1 sent
});

test('course completion is re-evaluated correctly when lessons are added', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'is_required' => true]);

    $action = new CompleteLessonAction();

    // Complete only lesson
    $action->execute($user, $lesson1);
    expect(Certificate::count())->toBe(1);

    // Add a NEW required lesson to the course
    $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'is_required' => true]);

    // User is no longer "complete" logically, but they have a certificate.
    // If they complete another lesson (newly added), certificates shouldn't double up.
    $action->execute($user, $lesson2);

    expect(Certificate::count())->toBe(1); // Still 1
});
