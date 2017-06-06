<?php

namespace App\Options;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordOptions
{
    /**
     * The guard the authentication should be made on.
     *
     * @var string
     */
    public $guard;

    /**
     * The form request validator to validate against.
     *
     * @var FormRequest
     */
    public $validator;

    /**
     * The field name identifier used in combination with the password.
     *
     * @var string
     */
    public $identifier = 'email';

    /**
     * The path to redirect after successfully performed the password reset email sending.
     *
     * @var
     */
    public $redirect = '/';

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