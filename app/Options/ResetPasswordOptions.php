<?php

namespace App\Options;

class ResetPasswordOptions
{
    /**
     * The path to redirect after successfully performed the password reset email sending.
     *
     * @var
     */
    public $redirectPath;

    /**
     * The field name identifier used in combination with the password.
     *
     * @var string
     */
    public $identifierField = 'email';

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
     * Set the $successRedirectPath to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $path
     * @return ResetPasswordOptions
     */
    public function setRedirectPath($path): ResetPasswordOptions
    {
        $this->redirectPath = $path;

        return $this;
    }

    /**
     * Set the $identifierField to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $name
     * @return ResetPasswordOptions
     */
    public function setIdentifierField($name): ResetPasswordOptions
    {
        $this->identifierField = $name;

        return $this;
    }
}