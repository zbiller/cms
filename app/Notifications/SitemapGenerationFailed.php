<?php

namespace App\Notifications;

use App\Mail\SitemapGenerationFailed as SitemapGenerationFailedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SitemapGenerationFailed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The error message the GenerateSitemapXml produced.
     *
     * @var string
     */
    public $message;

    /**
     * Set the error message.
     *
     * @param string|null $message
     */
    public function __construct($message = null)
    {
        $this->message = $message;
    }

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
        return (new SitemapGenerationFailedEmail($this->message))
            ->to($notifiable->email);
    }
}
