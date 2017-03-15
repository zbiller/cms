<?php

namespace App\Options;

class CanResetPasswordOptions
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
     * @return CanResetPasswordOptions
     */
    public static function instance(): CanResetPasswordOptions
    {
        return new static();
    }

    /**
     * Set the $successRedirectPath to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $path
     * @return CanResetPasswordOptions
     */
    public function setRedirectPath($path): CanResetPasswordOptions
    {
        $this->redirectPath = $path;

        return $this;
    }

    /**
     * Set the $identifierField to work with in the App\Traits\CanResetPassword trait.
     *
     * @param string $name
     * @return CanResetPasswordOptions
     */
    public function setIdentifierField($name): CanResetPasswordOptions
    {
        $this->identifierField = $name;

        return $this;
    }
}