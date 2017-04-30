<?php

namespace App\Options;

class AuthenticateOptions
{
    /**
     * The field name to be used in combination with the password to sign in the user.
     *
     * @var string
     */
    public $usernameField = 'username';

    /**
     * The path to redirect the user after login.
     *
     * @var string
     */
    public $loginRedirectPath = '/';

    /**
     * The path to redirect the user after logout.
     *
     * @var string
     */
    public $logoutRedirectPath = '/';

    /**
     * The number of tries a user is allowed to attempt login.
     *
     * @var int
     */
    public $maxLoginAttempts = 3;

    /**
     * The number of minutes to delay further login attempts.
     *
     * @var int
     */
    public $lockoutTime = 1;

    /**
     * Get a fresh instance of this class.
     *
     * @return AuthenticateOptions
     */
    public static function instance(): AuthenticateOptions
    {
        return new static();
    }

    /**
     * Set the $usernameField to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param string $name
     * @return AuthenticateOptions
     */
    public function setUsernameField($name): AuthenticateOptions
    {
        $this->usernameField = $name;

        return $this;
    }

    /**
     * Set the $logoutRedirectPath to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param string $path
     * @return AuthenticateOptions
     */
    public function setLogoutRedirectPath($path): AuthenticateOptions
    {
        $this->logoutRedirectPath = $path;

        return $this;
    }

    /**
     * Set the $loginRedirectPath to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param string $path
     * @return AuthenticateOptions
     */
    public function setLoginRedirectPath($path): AuthenticateOptions
    {
        $this->loginRedirectPath = $path;

        return $this;
    }

    /**
     * Set the throttleMaxLoginAttempts to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param int $tries
     * @return AuthenticateOptions
     */
    public function setMaxLoginAttempts($tries): AuthenticateOptions
    {
        $this->maxLoginAttempts = $tries;

        return $this;
    }

    /**
     * Set the $throttleLockoutTime to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param int $minutes
     * @return AuthenticateOptions
     */
    public function setLockoutTime($minutes): AuthenticateOptions
    {
        $this->lockoutTime = $minutes;

        return $this;
    }
}