<?php

namespace App\Options;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateOptions
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
    public $loginRedirect = '/';

    /**
     * The path to redirect the user after logout.
     *
     * @var string
     */
    public $logoutRedirect = '/';

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
     * An array containing additional condition constraints for the login to be a valid one.
     *
     * @var array
     */
    public $additionalConditions = [];

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
     * Set the $guard to work with in the App\Traits\CanAuthenticate trait.
     *
     * @param string $guard
     * @return AuthenticateOptions
     */
    public function setAuthGuard($guard): AuthenticateOptions
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Set the $validator to work with in the App\Traits\CanAuthenticate trait.
     *
     * @param FormRequest $validator
     * @return AuthenticateOptions
     */
    public function setValidator(FormRequest $validator): AuthenticateOptions
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the $usernameField to work with in the App\Traits\CanAuthenticate trait.
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
     * Set the $logoutRedirect to work with in the App\Traits\CanAuthenticate trait.
     *
     * @param string $path
     * @return AuthenticateOptions
     */
    public function setLogoutRedirect($path): AuthenticateOptions
    {
        $this->logoutRedirect = $path;

        return $this;
    }

    /**
     * Set the $loginRedirect to work with in the App\Traits\CanAuthenticate trait.
     *
     * @param string $path
     * @return AuthenticateOptions
     */
    public function setLoginRedirect($path): AuthenticateOptions
    {
        $this->loginRedirect = $path;

        return $this;
    }

    /**
     * Set the throttleMaxLoginAttempts to work with in the App\Traits\CanAuthenticate trait.
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
     * Set the $throttleLockoutTime to work with in the App\Traits\CanAuthenticate trait.
     *
     * @param int $minutes
     * @return AuthenticateOptions
     */
    public function setLockoutTime($minutes): AuthenticateOptions
    {
        $this->lockoutTime = $minutes;

        return $this;
    }

    /**
     * Set the $additionalConditions to work with in the App\Traits\CanAuthenticate trait.
     *
     * @param array $conditions
     * @return AuthenticateOptions
     */
    public function setAdditionalLoginConditions(array $conditions = []): AuthenticateOptions
    {
        $this->additionalConditions = $conditions;

        return $this;
    }
}