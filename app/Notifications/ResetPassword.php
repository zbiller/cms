<?php

namespace App\Notifications;

use App\Mail\PasswordRecovery;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The auth model instance.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * The user's reset password token.
     *
     * @var
     */
    public $token;

    /**
     * Set the token and the route.
     *
     * @param string $token
     * @param Authenticatable $user
     * @throws \App\Exceptions\EmailException
     */
    public function __construct(Authenticatable $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
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
        return (new PasswordRecovery('password-recovery', $this->user, $this->token))
            ->to($this->user->email);
    }
}
