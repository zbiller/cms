<?php

namespace App\Mail;

use App\Models\Cms\Email;
use App\Exceptions\EmailException;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordRecovery extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The email model.
     *
     * @var Email
     */
    protected $email;

    /**
     * Create a new message instance.
     *
     * @param string $identifier
     * @throws EmailException
     */
    public function __construct($identifier)
    {
        $this->email = Email::findByIdentifier($identifier);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown($this->email->getView(), $this->email->getData());
    }
}
