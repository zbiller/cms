<?php

namespace App\Notifications;

use App\Mail\SitemapGenerationSuccessful as SitemapGenerationSuccessfulEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SitemapGenerationSuccessful extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Establish the notification sending protocol.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    /**
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new SitemapGenerationSuccessfulEmail)
            ->to($notifiable->email);
    }
}
