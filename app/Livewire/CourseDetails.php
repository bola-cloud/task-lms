<?php

namespace App\Livewire;

use App\Actions\EnrollUserAction;
use App\Models\Course;
use App\Models\Enrollment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseDetails extends Component
{
    public Course $course;

    #[Layout('layouts.public')]
    public function mount(Course $course): void
    {
        if (!$course->is_published) {
            abort(404);
        }
        $this->course = $course;
    }

    /**
     * Enroll the authenticated user in the current course.
     */
    public function enroll(EnrollUserAction $enrollUserAction): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $enrollUserAction->execute(Auth::user(), $this->course);
            session()->flash('success', 'You have successfully enrolled! Start learning below.');
            return redirect()->route('courses.show', $this->course);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return null;
        }
    }

    #[Computed]
    public function isEnrolled(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Enrollment::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->exists();
    }

    #[Computed]
    public function lessons()
    {
        return $this->course->lessons;
    }

    public function render(): View
    {
        return view('livewire.course-details')
            ->title($this->course->title . ' – Career 180');
    }
}
