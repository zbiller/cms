<?php

/*
 * This is just a wrapper for the Laravel's AuthenticatesUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use App\Options\AuthenticatesUsersOptions;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers as AuthenticatesUsersBase;

trait AuthenticatesUsers
{
    use AuthenticatesUsersBase {
        logout as baseLogout;
    }

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\AuthenticatesUsersOptions file.
     *
     * @var AuthenticatesUsersOptions
     */
    protected $authenticatesUsersOptions;

    /**
     * The method used for setting the authentication options.
     * This method should be called inside the controller using this trait.
     * Inside the method, you should set all the authentication options.
     * This can be achieved using the methods from App\Options\AuthenticatesUsersOptions.
     *
     * @return AuthenticatesUsersOptions
     */
    abstract public function getAuthenticatesUsersOptions(): AuthenticatesUsersOptions;

    /**
     * Instantiate the $authenticatesUsersOptions property with the necessary authentication properties.
     *
     * @set $authenticatesUsersOptions
     */
    public function bootAuthenticatesUsers()
    {
        $this->authenticatesUsersOptions = $this->getAuthenticatesUsersOptions();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return $this->authenticatesUsersOptions->usernameField;
    }

    /**
     * Know where to redirect the user after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        return $this->authenticatesUsersOptions->loginRedirectPath;
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

        return redirect($this->authenticatesUsersOptions->logoutRedirectPath);
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
            $this->authenticatesUsersOptions->throttleMaxLoginAttempts,
            $this->authenticatesUsersOptions->throttleLockoutTime
        );
    }
}