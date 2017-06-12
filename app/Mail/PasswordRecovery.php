<?php

namespace App\Mail;

use App\Models\Cms\Email;
use App\Exceptions\EmailException;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordRecovery extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email model.
     *
     * @var Email
     */
    protected $email;

    /**
     * The loaded user model.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new message instance.
     *
     * @param string $identifier
     * @param Authenticatable $user
     * @throws EmailException
     */
    public function __construct($identifier, Authenticatable $user, $token)
    {
        $this->email = Email::findByIdentifier($identifier);
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws EmailException
     */
    public function build()
    {
        $this->replyTo($this->email->reply_to);
        $this->from($this->email->from_address, $this->email->from_name);
        $this->subject($this->email->subject ?: 'Password Reset');

        $this->markdown($this->email->getView(), [
            'message' => $this->parseMessage(),
        ]);

        if ($this->email->attachment && uploaded($this->email->attachment)->exists()) {
            $this->attach(uploaded($this->email->attachment)->path(null, true), [
                'as' => uploaded($this->email->attachment)->load()->original_name,
            ]);
        }

        return $this;
    }

    /**
     * Parse the message for used variables.
     * Replace the variable names with the relevant content.
     *
     * @return mixed
     */
    private function parseMessage()
    {
        $message = $this->email->message;

        $message = str_replace(
            '[full_name]',
            $this->user->full_name,
            $message
        );

        $message = str_replace(
            '[reset_password_url]',
            route($this->user->isAdmin() ? 'admin.password.change' : 'password.change', $this->token),
            $message
        );

        return $message;
    }
}
