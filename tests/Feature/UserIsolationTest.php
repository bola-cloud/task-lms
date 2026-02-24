<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Gate;

test('user cannot view non-free lesson if not enrolled', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'is_free_preview' => false
    ]);

    $this->actingAs($user);

    expect(Gate::allows('view', $lesson))->toBeFalse();
});

test('guest can view free preview lesson', function () {
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'is_free_preview' => true
    ]);

    expect(Gate::allows('view', $lesson))->toBeTrue();
});

test('enrolled user can view lesson', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['is_published' => true]);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'is_free_preview' => false
    ]);

    Enrollment::create(['user_id' => $user->id, 'course_id' => $course->id]);

    $this->actingAs($user);

    expect(Gate::allows('view', $lesson))->toBeTrue();
});
