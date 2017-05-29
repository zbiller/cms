<?php

namespace App\Notifications;

use App\Exceptions\EmailException;
use App\Mail\PasswordRecovery;
use App\Models\Cms\Email;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Mail;

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
        $email = Email::findByIdentifier('password-recovery');

        return (new MailMessage)->markdown($email->getView(), $email->getData([
            'url' => route($this->route, $this->token)
        ]));
    }
}
