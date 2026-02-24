<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Illuminate\View\View;

class CourseList extends Component
{
    #[Layout('layouts.public')]
    #[Title('Browse Courses – Career 180')]
    public function render(): View
    {
        return view('livewire.course-list');
    }

    /**
     * Get the list of published courses with lesson counts.
     */
    #[Computed]
    public function courses()
    {
        return Course::where('is_published', true)
            ->withCount('lessons')
            ->latest()
            ->get();
    }
}
