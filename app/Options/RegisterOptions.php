<?php

namespace App\Options;

use Exception;
use Illuminate\Foundation\Http\FormRequest;

class RegisterOptions
{
    /**
     * The guard the registration should be made on.
     *
     * @var string
     */
    private $guard;

    /**
     * The form request validator to validate against.
     *
     * @var FormRequest
     */
    private $validator;

    /**
     * Flag whether or not to verify the email of a newly registered user.
     *
     * @var bool
     */
    private $verify = true;

    /**
     * The path to redirect the user after register.
     *
     * @var string
     */
    private $registerRedirect = '/';

    /**
     * The path to redirect the user after email verification.
     *
     * @var string
     */
    private $verificationRedirect = '/';

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
     * @return RegisterOptions
     */
    public static function instance(): RegisterOptions
    {
        return new static();
    }

    /**
     * Set the $guard to work with in the App\Traits\CanRegister trait.
     *
     * @param string $guard
     * @return RegisterOptions
     */
    public function setAuthGuard($guard): RegisterOptions
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Set the $validator to work with in the App\Traits\CanRegister trait.
     *
     * @param FormRequest $validator
     * @return RegisterOptions
     */
    public function setValidator(FormRequest $validator): RegisterOptions
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the $verify to work with in the App\Traits\CanRegister trait.
     *
     * @return RegisterOptions
     */
    public function disableEmailVerification(): RegisterOptions
    {
        $this->verify = false;

        return $this;
    }

    /**
     * Set the $registerRedirect to work with in the App\Traits\CanRegister trait.
     *
     * @param string $path
     * @return RegisterOptions
     */
    public function setRegisterRedirect($path): RegisterOptions
    {
        $this->registerRedirect = $path;

        return $this;
    }

    /**
     * Set the $verificationRedirect to work with in the App\Traits\CanRegister trait.
     *
     * @param string $path
     * @return RegisterOptions
     */
    public function setVerificationRedirect($path): RegisterOptions
    {
        $this->verificationRedirect = $path;

        return $this;
    }
}