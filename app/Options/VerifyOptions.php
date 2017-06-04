<?php

namespace App\Options;

class VerifyOptions
{
    /**
     * Flag indicating whether or not the verification email should be sent immediately or queued.
     * If queueing mails is desired, the queues will need to be configured.
     *
     * @var bool
     */
    public $enableQueue = false;

    /**
     * Get a fresh instance of this class.
     *
     * @return VerifyOptions
     */
    public static function instance(): VerifyOptions
    {
        return new static();
    }

    /**
     * Set the $enableQueue to work with in the App\Traits\IsVerifiable trait.
     *
     * @return VerifyOptions
     */
    public function shouldQueueEmailSending(): VerifyOptions
    {
        $this->enableQueue = true;

        return $this;
    }
}