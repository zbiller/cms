<?php

namespace App\Options;

class RegisterOptions
{
    /**
     * The path to redirect the user after register.
     *
     * @var string
     */
    public $redirectPath = '/';

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
     * Set the $redirectPath to work with in the App\Traits\CanRegister trait.
     *
     * @param string $path
     * @return RegisterOptions
     */
    public function setRedirectPath($path): RegisterOptions
    {
        $this->redirectPath = $path;

        return $this;
    }
}