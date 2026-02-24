{{-- resources/views/livewire/course-details.blade.php --}}
<div x-data="{ openLessons: [] }">
    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 font-medium transition-colors">Courses</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-slate-900 font-semibold">{{ $course->title }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- LEFT: Course Info & Lessons --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Course Header --}}
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span
                            class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full uppercase">{{ $course->level }}</span>
                        <span
                            class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full">{{ $this->lessons->count() }}
                            Lessons</span>
                        @if($this->isEnrolled)
                            <span
                                class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Enrolled
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight mb-4">
                        {{ $course->title }}
                    </h1>
                    @if($course->description)
                        <p class="text-lg text-slate-600 leading-relaxed">{{ $course->description }}</p>
                    @endif
                </div>

                {{-- Curriculum / Accordion --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between p-6 border-b border-slate-100">
                        <h2 class="text-xl font-bold text-slate-900">Course Curriculum</h2>
                        <button
                            @click="openLessons = openLessons.length === {{ $this->lessons->count() }} ? [] : {{ $this->lessons->pluck('id')->toJson() }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                            Toggle All
                        </button>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($this->lessons as $lesson)
                            <div class="hover:bg-slate-50 transition-colors">
                                {{-- Accordion Header --}}
                                <div class="flex items-center justify-between p-4 sm:p-5 cursor-pointer"
                                    @click="openLessons.includes({{ $lesson->id }}) ? openLessons = openLessons.filter(i => i !== {{ $lesson->id }}) : openLessons.push({{ $lesson->id }})">

                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="flex-shrink-0 w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm flex items-center justify-center">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-semibold text-slate-900 truncate">{{ $lesson->title }}</h3>
                                            @if($lesson->is_free_preview)
                                                <span
                                                    class="text-xs font-bold text-emerald-600 uppercase tracking-wide flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                        <path fill-rule="evenodd"
                                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Free Preview
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                        @if($lesson->is_free_preview)
                                            <a href="{{ route('lessons.preview', [$this->course, $lesson]) }}" @click.stop
                                                class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                Watch Free
                                            </a>
                                        @elseif($this->isEnrolled)
                                            <a href="{{ route('lessons.show', [$this->course, $lesson]) }}" @click.stop
                                                class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                                                Start
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @endif

                                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                            :class="openLessons.includes({{ $lesson->id }}) ? 'rotate-180' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Accordion Body --}}
                                <div x-show="openLessons.includes({{ $lesson->id }})"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    class="px-5 pb-5">
                                    <p class="text-slate-500 text-sm pl-12">
                                        {{ $lesson->description ?? 'Watch this lesson to learn more about ' . $lesson->title . '.' }}
                                    </p>
                                    @if($lesson->is_free_preview)
                                        <div class="pl-12 mt-3">
                                            <a href="{{ route('lessons.preview', [$this->course, $lesson]) }}"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                Watch Preview
                                            </a>
                                        </div>
                                    @elseif($this->isEnrolled)
                                        <div class="pl-12 mt-3">
                                            <a href="{{ route('lessons.show', [$this->course, $lesson]) }}"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                                Start Lesson
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Enrollment Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-24">

                    {{-- Course Banner --}}
                    <div class="h-40 relative rounded-xl mb-6 overflow-hidden flex items-center justify-center">
                        @if($course->image)
                            <img src="{{ $course->image }}" alt="{{ $course->title }}"
                                class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-700"></div>
                        @endif

                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <svg class="w-full h-full" fill="none" viewBox="0 0 200 200">
                                <circle cx="150" cy="50" r="80" stroke="white" stroke-width="1" fill="none" />
                            </svg>
                        </div>

                        @if(!$course->image)
                            <svg class="w-14 h-14 text-white/80 relative z-10" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        @endif
                    </div>

                    {{-- Stats Row --}}
                    <div class="grid grid-cols-3 gap-3 mb-6 text-center">
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xl font-bold text-slate-900">{{ $this->lessons->count() }}</p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Lessons</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xl font-bold text-slate-900">
                                {{ $this->lessons->where('is_free_preview', true)->count() }}
                            </p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Free</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xl font-bold text-indigo-600">🏆</p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Certificate</p>
                        </div>
                    </div>

                    @if($this->isEnrolled)
                        <div
                            class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2 text-green-800 text-sm font-semibold">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            You are enrolled!
                        </div>
                        @if($this->lessons->isNotEmpty())
                            <a href="{{ route('lessons.show', [$course, $this->lessons->first()]) }}"
                                class="flex items-center justify-center w-full px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Continue Learning
                            </a>
                        @endif
                    @else
                        @auth
                            <button wire:click="enroll" wire:loading.attr="disabled"
                                class="flex items-center justify-center w-full px-6 py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 active:scale-95 transition-all mb-3 disabled:opacity-60">
                                <span wire:loading.remove wire:target="enroll">Enroll Now — It's Free</span>
                                <span wire:loading wire:target="enroll" class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        @else
                            <a href="{{ route('register') }}"
                                class="flex items-center justify-center w-full px-6 py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 active:scale-95 transition-all mb-3">
                                Enroll Now — It's Free
                            </a>
                        @endauth
                        <p class="text-center text-xs text-slate-400">🔒 Secure enrollment. Instant access to all lessons.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>