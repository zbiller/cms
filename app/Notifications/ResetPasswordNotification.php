<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    /**
     * The user's reset password token.
     *
     * @var
     */
    public $token;

    /**
     * The route to redirect the user when clicking to reset the password.
     *
     * @var
     */
    public $route;

    /**
     * Set the token and the route.
     *
     * @param string $token
     * @param string $route
     */
    public function __construct($token, $route)
    {
        $this->token = $token;
        $this->route = $route;
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
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', route($this->route, $this->token))
            ->line('If you did not request a password reset, no further action is required.');
    }
}
