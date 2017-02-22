<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    /**
     * @var
     */
    public $token;

    /**
     * @var
     */
    public $route;

    /**
     * @param string $token
     * @param string $route
     */
    public function __construct($token, $route)
    {
        $this->token = $token;
        $this->route = $route;
    }

    /**
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    /**
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', route($this->route, $this->token))
            ->line('If you did not request a password reset, no further action is required.');
    }
}
