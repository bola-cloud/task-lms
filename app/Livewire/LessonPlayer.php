<?php

namespace App\Livewire;

use App\Actions\CompleteLessonAction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LessonPlayer extends Component
{
    public Course $course;
    public Lesson $lesson;
    public bool $isEnrolled = false;
    public bool $isPreview = false;

    #[Layout('layouts.public')]
    public function mount(Course $course, Lesson $lesson)
    {
        $this->course = $course;
        $this->lesson = $lesson;
        $this->isPreview = request()->routeIs('lessons.preview');

        if (!$this->isPreview) {
            $this->isEnrolled = Auth::check() && Enrollment::where('user_id', Auth::id())
                ->where('course_id', $this->course->id)
                ->exists();

            if (!$this->isEnrolled) {
                return redirect()->route('courses.show', $this->course)
                    ->with('error', 'You must be enrolled to view this lesson.');
            }

            // Track lesson start
            LessonProgress::firstOrCreate(
                ['user_id' => Auth::id(), 'lesson_id' => $this->lesson->id],
                ['started_at' => now()]
            );
        } elseif (!$this->lesson->is_free_preview) {
            return redirect()->route('courses.show', $this->course)
                ->with('error', 'This lesson is not available for preview.');
        }

        return null;
    }

    /**
     * Mark the current lesson as completed and move to the next one.
     */
    public function markAsCompleted(CompleteLessonAction $completeLessonAction): mixed
    {
        if ($this->isPreview) {
            return null;
        }

        $completeLessonAction->execute(Auth::user(), $this->lesson);
        session()->flash('success', 'Lesson completed! Great work 🎉');

        // Redirect to next lesson if exists
        $nextLesson = $this->course->lessons()
            ->where('order', '>', $this->lesson->order)
            ->first();

        if ($nextLesson) {
            return redirect()->route('lessons.show', [$this->course, $nextLesson]);
        }

        return redirect()->route('courses.show', $this->course)
            ->with('success', 'You have completed all lessons! Check your certificate. 🏆');
    }

    #[Computed]
    public function completedLessonIds(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return LessonProgress::where('user_id', Auth::id())
            ->whereIn('lesson_id', $this->course->lessons->pluck('id'))
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->toArray();
    }

    #[Computed]
    public function progressPercentage(): int
    {
        $lessons = $this->course->lessons;
        $totalRequired = $lessons->where('is_required', true)->count() ?: $lessons->count();

        if ($totalRequired === 0) {
            return 0;
        }

        $completedRequired = $lessons
            ->where('is_required', true)
            ->whereIn('id', $this->completedLessonIds)
            ->count();

        return (int) round(($completedRequired / $totalRequired) * 100);
    }

    #[Computed]
    public function lessons()
    {
        return $this->course->lessons;
    }

    public function render(): View
    {
        return view('livewire.lesson-player')
            ->title($this->lesson->title . ' – ' . $this->course->title);
    }
}
