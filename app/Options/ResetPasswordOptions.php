<?php

namespace App\Options;

use Exception;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordOptions
{
    /**
     * The guard the authentication should be made on.
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
     * The field name identifier used in combination with the password.
     *
     * @var string
     */
    private $identifier = 'email';

    /**
     * The path to redirect after successfully performed the password reset email sending.
     *
     * @var
     */
    private $redirect = '/';

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
     * @return ResetPasswordOptions
     */
    public static function instance(): ResetPasswordOptions
    {
        return new static();
    }

    /**
     * Set the $guard to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $guard
     * @return ResetPasswordOptions
     */
    public function setAuthGuard($guard): ResetPasswordOptions
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Set the $validator to work with in the App\Traits\CanResetPassword trait.
     *
     * @param FormRequest $validator
     * @return ResetPasswordOptions
     */
    public function setValidator(FormRequest $validator): ResetPasswordOptions
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the $identifier to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $field
     * @return ResetPasswordOptions
     */
    public function setIdentifier($field): ResetPasswordOptions
    {
        $this->identifier = $field;

        return $this;
    }

    /**
     * Set the $redirect to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $path
     * @return ResetPasswordOptions
     */
    public function setRedirect($path): ResetPasswordOptions
    {
        $this->redirect = $path;

        return $this;
    }
}