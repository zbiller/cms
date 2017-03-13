<?php

namespace App\Options;

class AuthenticatesUsersOptions
{
    /**
     * The field name to be used in combination with the password to sign in the user.
     *
     * @var string
     */
    public $usernameField = 'email';

    /**
     * The path to redirect the user after login.
     *
     * @var string
     */
    public $loginRedirectPath = '/admin';

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
    public $throttleMaxLoginAttempts = 3;

    /**
     * The number of minutes to delay further login attempts.
     *
     * @var int
     */
    public $throttleLockoutTime = 1;

    /**
     * Get a fresh instance of this class.
     *
     * @return AuthenticatesUsersOptions
     */
    public static function instance(): AuthenticatesUsersOptions
    {
        return new static();
    }

    /**
     * Set the $usernameField to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param string $name
     * @return AuthenticatesUsersOptions
     */
    public function setUsernameField($name): AuthenticatesUsersOptions
    {
        $this->usernameField = $name;

        return $this;
    }

    /**
     * Set the $logoutRedirectPath to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param string $path
     * @return AuthenticatesUsersOptions
     */
    public function setLogoutRedirectPath($path): AuthenticatesUsersOptions
    {
        $this->logoutRedirectPath = $path;

        return $this;
    }

    /**
     * Set the $loginRedirectPath to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param string $path
     * @return AuthenticatesUsersOptions
     */
    public function setLoginRedirectPath($path): AuthenticatesUsersOptions
    {
        $this->loginRedirectPath = $path;

        return $this;
    }

    /**
     * Set the throttleMaxLoginAttempts to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param int $tries
     * @return AuthenticatesUsersOptions
     */
    public function setThrottleMaxLoginAttempts($tries): AuthenticatesUsersOptions
    {
        $this->throttleMaxLoginAttempts = $tries;

        return $this;
    }

    /**
     * Set the $throttleLockoutTime to work with in the App\Traits\AuthenticatesUsers trait.
     *
     * @param int $minutes
     * @return AuthenticatesUsersOptions
     */
    public function setThrottleLockoutTime($minutes): AuthenticatesUsersOptions
    {
        $this->throttleLockoutTime = $minutes;

        return $this;
    }
}