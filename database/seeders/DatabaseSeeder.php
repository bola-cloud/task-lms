<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@career180.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // Create a regular learner
        User::factory()->create([
            'name' => 'Learner User',
            'email' => 'learner@career180.com',
            'password' => bcrypt('password'),
        ]);

        $courses = [
            [
                'title' => 'Laravel 11 Fundamentals',
                'description' => 'Master the basics of Laravel 11 with this comprehensive course.',
                'level' => 'Beginner',
                'is_published' => true,
                'lessons' => [
                    ['title' => 'Introduction to Laravel', 'video_url' => 'https://vimeo.com/76979871', 'is_free_preview' => true],
                    ['title' => 'Routing & Controllers', 'video_url' => 'https://vimeo.com/76979871', 'is_free_preview' => false],
                    ['title' => 'Eloquent ORM', 'video_url' => 'https://vimeo.com/76979871', 'is_free_preview' => false],
                ],
            ],
            [
                'title' => 'Advanced Livewire Patterns',
                'description' => 'Take your Livewire skills to the next level.',
                'level' => 'Advanced',
                'is_published' => true,
                'lessons' => [
                    ['title' => 'Alpine.js Integration', 'video_url' => 'https://vimeo.com/76979871', 'is_free_preview' => true],
                    ['title' => 'Computed Properties and Actions', 'video_url' => 'https://vimeo.com/76979871', 'is_free_preview' => false],
                ],
            ],
            [
                'title' => 'Unpublished Mystery Course',
                'description' => 'This course should not be visible to learners.',
                'level' => 'Intermediate',
                'is_published' => false,
                'lessons' => [
                    ['title' => 'Secret Lesson', 'video_url' => 'https://vimeo.com/76979871', 'is_free_preview' => false],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            unset($courseData['lessons']);

            $courseData['slug'] = Str::slug($courseData['title']);
            $course = Course::create($courseData);

            foreach ($lessons as $index => $lessonData) {
                $lessonData['slug'] = Str::slug($lessonData['title']);
                $lessonData['order'] = $index + 1;
                $course->lessons()->create($lessonData);
            }
        }
    }
}
