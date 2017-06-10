<?php

namespace App\Mail;

use App\Models\Cms\Email;
use App\Exceptions\EmailException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
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
        $this->subject($this->email && $this->email->subject ? $this->email->subject : 'Password Reset');
        $this->markdown($this->email->getView(), $this->email->getData([
            'url' => route($this->user->isAdmin() ? 'admin.password.change' : 'password.change', $this->token)
        ]));

        return $this;
    }
}
