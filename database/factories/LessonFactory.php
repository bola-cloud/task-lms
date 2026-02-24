<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'course_id' => Course::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'video_url' => 'https://vimeo.com/76979871',
            'order' => 1,
            'is_free_preview' => false,
            'is_required' => true,
        ];
    }
}
