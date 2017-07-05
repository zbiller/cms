<?php

namespace App\Mail;

use Crypt;
use App\Models\Cms\Email;
use App\Exceptions\EmailException;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerification extends Mailable implements ShouldQueue
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
     * Create a new message instance.
     *
     * @param Authenticatable $user
     */
    public function __construct($identifier, Authenticatable $user)
    {
        $this->email = Email::findByIdentifier($identifier);
        $this->user = $user;
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
        $this->subject($this->email->subject ?: 'Email Verification');

        $this->markdown($this->email->getView(), [
            'message' => $this->parseMessage(),
        ]);

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
            '[first_name]',
            $this->user->first_name,
            $message
        );

        $message = str_replace(
            '[last_name]',
            $this->user->last_name,
            $message
        );

        $message = str_replace(
            '[full_name]',
            $this->user->full_name,
            $message
        );

        $message = str_replace(
            '[email_verification_url]',
            route('register.verify', [
                'token' => Crypt::encrypt($this->user->verification_token),
                'email' => Crypt::encrypt($this->user->email),
            ]),
            $message
        );

        return $message;
    }
}
