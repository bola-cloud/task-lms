{{-- resources/views/livewire/course-list.blade.php --}}
<div>
    {{-- Hero --}}
    <div class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <span
                class="inline-block px-4 py-1.5 bg-white/20 rounded-full text-sm font-semibold tracking-wide uppercase mb-4">Career
                180 Learning Platform</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold mb-4 leading-tight">Grow Your Career, <br><span
                    class="text-yellow-300">One Lesson at a Time</span></h1>
            <p class="text-indigo-200 text-lg max-w-2xl mx-auto">Browse our expert-led video courses and build
                real-world skills. Start learning for free today.</p>
        </div>
    </div>

    {{-- Course Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">All Courses</h2>
                <p class="text-slate-500 mt-1">{{ $this->courses->count() }} courses available</p>
            </div>
        </div>

        @if($this->courses->isEmpty())
            <div class="text-center py-24 text-slate-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
                <p class="text-xl font-semibold">No courses available yet</p>
                <p class="text-sm mt-2">Check back soon!</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $gradients = [
                    'from-indigo-500 to-purple-600',
                    'from-blue-500 to-cyan-600',
                    'from-emerald-500 to-teal-600',
                    'from-orange-500 to-rose-500',
                    'from-pink-500 to-purple-500',
                    'from-sky-500 to-blue-600',
                ];
            @endphp

            @foreach($this->courses as $i => $course)
                <a href="{{ route('courses.show', $course) }}"
                    class="group bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    {{-- Card Banner --}}
                    <div class="h-44 relative overflow-hidden flex items-end p-5">
                        @if($course->image)
                            <img src="{{ $course->image }}" alt="{{ $course->title }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br {{ $gradients[$i % count($gradients)] }}"></div>
                        @endif

                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <svg class="w-full h-full" fill="none" viewBox="0 0 200 200">
                                <circle cx="150" cy="50" r="80" stroke="white" stroke-width="1" fill="none" />
                                <circle cx="50" cy="150" r="60" stroke="white" stroke-width="1" fill="none" />
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <span
                                class="inline-block px-3 py-1 bg-white/25 text-white text-xs font-bold rounded-full uppercase tracking-wider backdrop-blur-sm">
                                {{ $course->level }}
                            </span>
                        </div>
                        <div
                            class="absolute top-4 right-4 text-white/70 text-sm font-medium z-10 bg-black/10 px-2 py-0.5 rounded-md backdrop-blur-sm">
                            {{ $course->lessons_count }} lessons
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6 flex flex-col flex-grow">
                        <h3
                            class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors leading-tight">
                            {{ $course->title }}
                        </h3>
                        <p class="text-slate-500 text-sm line-clamp-2 flex-grow mb-4">
                            {{ $course->description ?? 'An excellent course for expanding your professional skills.' }}
                        </p>
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                {{ $course->lessons_count }} {{ Str::plural('Lesson', $course->lessons_count) }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 text-indigo-600 text-sm font-semibold group-hover:gap-2.5 transition-all">
                                View Course
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>