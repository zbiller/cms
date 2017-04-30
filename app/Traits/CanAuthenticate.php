<?php

/*
 * This is just a wrapper for the Laravel's AuthenticatesUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use Exception;
use ReflectionMethod;
use App\Options\AuthenticateOptions;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

trait CanAuthenticate
{
    use AuthenticatesUsers {
        logout as baseLogout;
    }

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\AuthenticateOptions file.
     *
     * @var AuthenticateOptions
     */
    protected static $authenticationOptions;

    /**
     * Instantiate the $AuthenticateOptions property with the necessary authentication properties.
     *
     * @set $AuthenticateOptions
     */
    public static function bootCanAuthenticate()
    {
        self::checkAuthenticateOptions();

        self::$authenticationOptions = self::getAuthenticateOptions();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return self::$authenticationOptions->usernameField;
    }

    /**
     * Know where to redirect the user after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        if ($this->hasIntendedRedirectUrl()) {
            return $this->getIntendedRedirectUrl();
        }

        return self::$authenticationOptions->loginRedirectPath;
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->baseLogout($request);

        return redirect(self::$authenticationOptions->logoutRedirectPath);
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            self::$authenticationOptions->maxLoginAttempts,
            self::$authenticationOptions->lockoutTime
        );
    }

    /**
     * Set the intended url to redirect after successful login.
     *
     * @return void
     */
    protected function setIntendedRedirectUrl()
    {
        if (str_contains(url()->previous(), self::$authenticationOptions->loginRedirectPath)) {
            session()->flash('intended_redirect', url()->previous());
        }
    }

    /**
     * Get the intended url to redirect after successful login.
     *
     * @return string
     */
    protected function getIntendedRedirectUrl()
    {
        return session('intended_redirect');
    }

    /**
     * Verify if an intended redirect url exists.
     *
     * @return string
     */
    protected function hasIntendedRedirectUrl()
    {
        return session()->has('intended_redirect');
    }

    /**
     * Verify if the getAuthenticateOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkAuthenticateOptions()
    {
        if (!method_exists(self::class, 'getAuthenticateOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getAuthenticateOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getAuthenticateOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getAuthenticateOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}