<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PendingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The description of the notification.
     *
     * @var string
     */
    public $subject;

    /**
     * The url to the "to be approved" entity in the admin.
     *
     * @var string
     */
    public $url;

    /**
     * Create a new notification instance.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->subject = 'A new record requires your approval';
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'mail',
            'database',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->line('You have a new record pending your approval.')
            ->action('Review the record', $this->url)
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->subject,
            'url' => $this->url,
        ];
    }
}
