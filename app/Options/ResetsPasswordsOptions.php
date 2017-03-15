<?php

namespace App\Options;

class ResetsPasswordsOptions
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
     * @return ResetsPasswordsOptions
     */
    public static function instance(): ResetsPasswordsOptions
    {
        return new static();
    }

    /**
     * Set the $successRedirectPath to work with in the App\Traits\ResetsPasswords trait.
     *
     * @param string $path
     * @return ResetsPasswordsOptions
     */
    public function setRedirectPath($path): ResetsPasswordsOptions
    {
        $this->redirectPath = $path;

        return $this;
    }

    /**
     * Set the $identifierField to work with in the App\Traits\ResetsPasswords trait.
     *
     * @param string $name
     * @return ResetsPasswordsOptions
     */
    public function setIdentifierField($name): ResetsPasswordsOptions
    {
        $this->identifierField = $name;

        return $this;
    }
}