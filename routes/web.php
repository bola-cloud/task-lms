<?php

use App\Livewire\CourseList;
use App\Livewire\CourseDetails;
use App\Livewire\LessonPlayer;

Route::get('/', CourseList::class)->name('home');

Route::get('/courses/{course}', CourseDetails::class)->name('courses.show');

Route::get('/courses/{course}/lessons/{lesson}', LessonPlayer::class)
    ->middleware(['auth']) // Progress tracking requires auth
    ->name('lessons.show');

// Separate route for free previews accessible to guests
Route::get('/courses/{course}/preview/{lesson}', LessonPlayer::class)
    ->name('lessons.preview');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/settings.php';