<x-mail::message>
    # Welcome to Career 180!

    Hi {{ $user->name }},

    We're excited to have you on board. Start your career journey today by exploring our courses.

    <x-mail::button :url="config('app.url') . '/courses'">
        Browse Courses
    </x-mail::button>

    Happy learning!<br>
    {{ config('app.name') }}
</x-mail::message>