{{-- resources/views/livewire/lesson-player.blade.php --}}

{{-- Load Plyr styles --}}
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<div class="min-h-[calc(100vh-64px)]" x-data="{
        showModal: false,
        progress: {{ $this->progressPercentage }},
        player: null,
        initPlayer() {
            this.player = new Plyr(this.$refs.player, {
                controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            });
            this.player.on('ended', () => {
                @if(!$isPreview)
                    this.showModal = true;
                @endif
            });
        }
    }" x-init="initPlayer()">
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm text-slate-500 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Courses</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('courses.show', $course) }}"
                    class="hover:text-indigo-600 transition-colors font-medium">{{ $course->title }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-slate-900 font-semibold truncate">{{ $lesson->title }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col xl:flex-row gap-8">

            {{-- MAIN: Video Player --}}
            <div class="flex-grow min-w-0">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-6 leading-tight">
                    @if($isPreview)
                        <span
                            class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 text-sm font-bold rounded-md mr-2 align-middle">FREE
                            PREVIEW</span>
                    @endif
                    {{ $lesson->title }}
                </h1>

                {{-- Plyr Player --}}
                <div class="aspect-video bg-slate-900 rounded-2xl overflow-hidden shadow-2xl mb-6">
                    <div x-ref="player" data-plyr-provider="vimeo"
                        data-plyr-embed-id="{{ $lesson->video_url ? Str::afterLast($lesson->video_url, '/') : '76979871' }}">
                    </div>
                </div>

                {{-- Lesson Status Bar --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div>
                        <p class="font-semibold text-slate-900 mb-1">
                            @if(in_array($lesson->id, $this->completedLessonIds))
                                ✅ Lesson Completed
                            @elseif($isPreview)
                                👀 Preview Mode
                            @else
                                🎬 Now Watching
                            @endif
                        </p>
                        <p class="text-sm text-slate-500">
                            @if(in_array($lesson->id, $this->completedLessonIds))
                                You've already finished this lesson.
                            @elseif($isPreview)
                                Enroll in the course to track your progress.
                            @else
                                Watch the video, then mark it as completed.
                            @endif
                        </p>
                    </div>

                    @if(!in_array($lesson->id, $this->completedLessonIds) && !$isPreview)
                        <button @click="showModal = true"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 active:scale-95 transition-all whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Mark as Completed
                        </button>
                    @elseif(in_array($lesson->id, $this->completedLessonIds))
                        <span
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-100 text-green-700 font-semibold rounded-xl text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Completed ✓
                        </span>
                    @endif
                </div>

                @if($isPreview)
                    <div
                        class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-sm text-indigo-800 flex items-start gap-3">
                        <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-semibold mb-1">You're watching a free preview</p>
                            <p class="text-indigo-700"><a href="{{ route('courses.show', $course) }}"
                                    class="underline font-semibold hover:text-indigo-900">Enroll in the full course</a> to
                                unlock all lessons and earn your certificate.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- SIDEBAR: Progress & Lesson List --}}
            <div class="w-full xl:w-80 flex-shrink-0">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                    {{-- Progress Header --}}
                    <div class="p-5 border-b border-slate-100">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-slate-900">Your Progress</h3>
                            <span class="text-sm font-bold text-indigo-600" x-text="Math.round(progress) + '%'"></span>
                        </div>
                        {{-- Animated Progress Bar --}}
                        <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-700 ease-out"
                                :style="'width: ' + progress + '%'">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">
                            {{ count($this->completedLessonIds) }} of {{ $this->lessons->count() }} lessons completed
                        </p>
                    </div>

                    {{-- Lesson List --}}
                    <div class="overflow-y-auto max-h-[60vh]">
                        @foreach($this->lessons as $idx => $sideLesson)
                            @php
                                $isDone = in_array($sideLesson->id, $this->completedLessonIds);
                                $isCurrent = $lesson->id === $sideLesson->id;
                                $href = $sideLesson->is_free_preview
                                    ? route('lessons.preview', [$course, $sideLesson])
                                    : ($this->isEnrolled ? route('lessons.show', [$course, $sideLesson]) : '#');
                            @endphp
                            <a href="{{ $href }}"
                                class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-50 hover:bg-slate-50 transition-colors {{ $isCurrent ? 'bg-indigo-50 border-l-4 border-l-indigo-500' : '' }} {{ (!$isEnrolled && !$sideLesson->is_free_preview) ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">

                                {{-- Status Icon --}}
                                <div class="flex-shrink-0">
                                    @if($isDone)
                                        <div class="w-7 h-7 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($isCurrent)
                                        <div class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                        </div>
                                    @elseif(!$isEnrolled && !$sideLesson->is_free_preview)
                                        <div class="w-7 h-7 bg-slate-200 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-xs font-bold text-slate-500">
                                            {{ $idx + 1 }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="text-sm font-semibold {{ $isCurrent ? 'text-indigo-700' : 'text-slate-700' }} truncate">
                                        {{ $sideLesson->title }}
                                    </p>
                                    @if($sideLesson->is_free_preview)
                                        <p class="text-xs text-emerald-600 font-medium">Free Preview</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showModal = false">
        </div>

        {{-- Modal Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-9 h-9 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h3 class="text-xl font-extrabold text-slate-900 mb-2">Ready to Mark Complete?</h3>
            <p class="text-slate-500 mb-8">Did you finish watching and feel confident in the material? This will update
                your course progress.</p>

            <div class="flex gap-3">
                <button @click="showModal = false"
                    class="flex-1 px-5 py-3 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                    Not Yet
                </button>
                <button wire:click="markAsCompleted" @click="showModal = false"
                    class="flex-1 px-5 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors">
                    Yes, I'm Done! ✓
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Load Plyr JS after the DOM --}}
<script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>