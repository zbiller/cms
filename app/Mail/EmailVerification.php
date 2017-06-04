<?php

namespace App\Mail;

use App\Models\Cms\Email;
use Crypt;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class EmailVerification extends Mailable
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
     */
    public function build()
    {
        $this->subject($this->email && $this->email->subject ? $this->email->subject : 'Account Verification');
        $this->markdown($this->email->getView(), $this->email->getData([
            'url' => route('register.verify', [
                'token' => Crypt::encrypt($this->user->verification_token),
                'email' => Crypt::encrypt($this->user->email),
            ]),
        ]));

        return $this;
    }
}
