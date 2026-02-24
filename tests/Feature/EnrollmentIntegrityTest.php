<?php

namespace Tests\Feature;

use App\Actions\EnrollUserAction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Exception;

test('user can enroll in a published course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);

    $action = new EnrollUserAction();
    $enrollment = $action->execute($user, $course);

    expect($enrollment)->toBeInstanceOf(Enrollment::class);
    expect($enrollment->user_id)->toBe($user->id);
    expect($enrollment->course_id)->toBe($course->id);
});

test('user cannot enroll in an unpublished course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => false]);

    $action = new EnrollUserAction();

    expect(fn() => $action->execute($user, $course))->toThrow(Exception::class);
});

test('enrollment is idempotent and handles race conditions via unique constraint', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);

    $action = new EnrollUserAction();

    // First enrollment
    $action->execute($user, $course);

    // Immediate second enrollment (should return same record as per action logic)
    $enrollment = $action->execute($user, $course);

    expect(Enrollment::count())->toBe(1);
    expect($enrollment->id)->toBe(Enrollment::first()->id);
});

test('enrollment prevents duplicates even if action check is bypassed', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);

    Enrollment::create(['user_id' => $user->id, 'course_id' => $course->id]);

    // Manual attempt to bypass action logic and create duplicate
    expect(fn() => Enrollment::create(['user_id' => $user->id, 'course_id' => $course->id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});