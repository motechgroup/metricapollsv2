@section('page_title', 'Metrica Academy')

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Metrica Research Academy</h1>
        <p class="text-sm text-gray-500">Master field survey practices and data ethics to unlock exclusive high-paying research panels.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if($activeCourseId)
        <!-- Lesson Reader -->
        <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm max-w-2xl mx-auto space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <div>
                    <span class="text-xxs font-bold text-gray-400 uppercase font-mono">Course Lesson</span>
                    <h2 class="text-sm font-bold text-gray-950 mt-0.5">{{ $activeCourse->title }}</h2>
                </div>
                <button wire:click="cancelCourse" class="text-xs font-semibold text-gray-500 hover:text-gray-900">Exit Course</button>
            </div>

            <!-- Progress tracker -->
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-gray-500 uppercase">
                    <span>Lesson Progress</span>
                    <span>Page {{ $currentLessonIndex + 1 }} of {{ count($activeCourse->lessons) }}</span>
                </div>
                <div class="w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                    <div class="bg-gray-900 h-full rounded-full transition-all duration-300" style="width: {{ (($currentLessonIndex + 1) / count($activeCourse->lessons)) * 100 }}%"></div>
                </div>
            </div>

            <!-- Lesson Content -->
            <div class="space-y-4 pt-4 leading-relaxed text-gray-700 text-sm">
                <h3 class="text-lg font-bold text-gray-900">{{ $activeCourse->lessons[$currentLessonIndex]['title'] }}</h3>
                <p>{{ $activeCourse->lessons[$currentLessonIndex]['content'] }}</p>
            </div>

            <!-- Action footer -->
            <div class="border-t border-gray-100 pt-6 text-right">
                <button wire:click="nextLesson" class="rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    {{ ($currentLessonIndex + 1 < count($activeCourse->lessons)) ? 'Next Lesson' : 'Complete & Graduate' }}
                </button>
            </div>
        </div>
    @else
        <!-- Available Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($courses as $course)
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start gap-4">
                        <h2 class="text-base font-bold text-gray-900">{{ $course->title }}</h2>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-900 font-mono">+{{ $course->points_award }} pts</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ $course->description }}</p>
                </div>

                <div class="border-t border-gray-100 mt-6 pt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-400 font-medium">Lessons: {{ count($course->lessons) }} topics</span>
                    @if(in_array($course->id, $completedCourseIds))
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Graduated</span>
                    @else
                        <button wire:click="selectCourse({{ $course->id }})" class="rounded-md bg-gray-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Start Learning
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
