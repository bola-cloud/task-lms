<?php

namespace App\Listeners;

use App\Actions\GenerateCertificateAction;
use App\Events\CourseCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourseCompletionMail;

class IssueCertificate implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected GenerateCertificateAction $generateCertificateAction
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(CourseCompleted $event): void
    {
        // Issue certificate (Action handles idempotency)
        $certificate = $this->generateCertificateAction->execute($event->user, $event->course);

        // Check if we should send the email (if it's the first time)
        // In a real app, we might track if the email was sent, 
        // but GenerateCertificateAction only returns a NEW model if it didn't exist,
        // or we can check wasRecentlyCreated.

        if ($certificate->wasRecentlyCreated) {
            Mail::to($event->user->email)->send(new CourseCompletionMail($event->user, $event->course, $certificate));
        }
    }
}
