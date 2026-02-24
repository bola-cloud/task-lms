<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Support\Str;

test('course slugs must be unique', function () {
    $title = 'My Awesome Course';
    $slug = Str::slug($title);

    Course::create([
        'title' => $title,
        'slug' => $slug,
        'level' => 'Beginner'
    ]);

    expect(fn() => Course::create([
        'title' => 'Another Course',
        'slug' => $slug, // Same slug
        'level' => 'Advanced'
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('soft deletes handle slug uniqueness correctly', function () {
    $slug = 'awesome-course';

    $course = Course::create([
        'title' => 'Title 1',
        'slug' => $slug,
        'level' => 'Beginner'
    ]);

    $course->delete(); // Soft delete

    expect(fn() => Course::create([
        'title' => 'Title 2',
        'slug' => $slug,
        'level' => 'Advanced'
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});
