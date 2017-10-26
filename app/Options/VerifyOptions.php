<?php

namespace App\Options;

use Exception;

class VerifyOptions
{
    /**
     * Flag indicating whether or not the verification email should be sent immediately or queued.
     * If queueing mails is desired, the queues will need to be configured.
     *
     * @var bool
     */
    private $enableQueue = false;

    /**
     * Get the value of a property of this class.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists(static::class, $name)) {
            return $this->{$name};
        }

        throw new Exception(
            'The property "' . $name . '" does not exist in class "' . static::class . '"'
        );
    }

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