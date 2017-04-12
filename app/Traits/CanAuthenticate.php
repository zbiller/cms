<?php

/*
 * This is just a wrapper for the Laravel's AuthenticatesUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */
namespace App\Traits;

use App\Options\AuthenticationOptions;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

trait CanAuthenticate
{
    use ChecksTrait;
    use AuthenticatesUsers {
        logout as baseLogout;
    }

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\AuthenticationOptions file.
     *
     * @var AuthenticationOptions
     */
    protected static $authenticationOptions;

    /**
     * Instantiate the $AuthenticationOptions property with the necessary authentication properties.
     *
     * @set $AuthenticationOptions
     */
    public static function bootCanAuthenticate()
    {
        self::checkOptionsMethodDeclaration('getAuthenticationOptions');

        self::$authenticationOptions = self::getAuthenticationOptions();
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
}