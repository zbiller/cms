<?php

namespace App\Options;

class RegisterOptions
{
    /**
     * The path to redirect the user after register.
     *
     * @var string
     */
    public $registerRedirectPath = '/';

    /**
     * The path to redirect the user after email verification.
     *
     * @var string
     */
    public $verificationRedirectPath = '/';

    /**
     * Flag whether or not to verify the email of a newly registered user.
     *
     * @var bool
     */
    public $verifyEmail = true;

    /**
     * Get a fresh instance of this class.
     *
     * @return RegisterOptions
     */
    public static function instance(): RegisterOptions
    {
        return new static();
    }

    /**
     * Set the $registerRedirectPath to work with in the App\Traits\CanRegister trait.
     *
     * @param string $path
     * @return RegisterOptions
     */
    public function setRegisterRedirectPath($path): RegisterOptions
    {
        $this->registerRedirectPath = $path;

        return $this;
    }

    /**
     * Set the $verificationRedirectPath to work with in the App\Traits\CanRegister trait.
     *
     * @param string $path
     * @return RegisterOptions
     */
    public function setVerificationRedirectPath($path): RegisterOptions
    {
        $this->verificationRedirectPath = $path;

        return $this;
    }

    /**
     * Set the $verifyEmail to work with in the App\Traits\CanRegister trait.
     *
     * @return RegisterOptions
     */
    public function disableEmailVerification(): RegisterOptions
    {
        $this->verifyEmail = false;

        return $this;
    }
}