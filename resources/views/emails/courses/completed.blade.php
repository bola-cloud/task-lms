<x-mail::message>
    # Congratulations, {{ $user->name }}!

    You have successfully completed the course **{{ $course->title }}**.

    Your certificate ID is: `{{ $certificate->uuid }}`

    <x-mail::button :url="config('app.url') . '/certificates/' . $certificate->uuid">
        View Certificate
    </x-mail::button>

    Thanks for learning with us!<br>
    {{ config('app.name') }}
</x-mail::message>