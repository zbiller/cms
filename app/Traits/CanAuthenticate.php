<?php

/*
 * This is just a wrapper for the Laravel's AuthenticatesUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use App\Options\CanAuthenticateOptions;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

trait CanAuthenticate
{
    use AuthenticatesUsers {
        logout as baseLogout;
    }

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\CanAuthenticateOptions file.
     *
     * @var CanAuthenticateOptions
     */
    protected $canAuthenticateOptions;

    /**
     * The method used for setting the authentication options.
     * This method should be called inside the controller using this trait.
     * Inside the method, you should set all the authentication options.
     * This can be achieved using the methods from App\Options\CanAuthenticateOptions.
     *
     * @return CanAuthenticateOptions
     */
    abstract public function getCanAuthenticateOptions(): CanAuthenticateOptions;

    /**
     * Instantiate the $CanAuthenticateOptions property with the necessary authentication properties.
     *
     * @set $CanAuthenticateOptions
     */
    public function bootAuthenticatesUsers()
    {
        $this->canAuthenticateOptions = $this->getCanAuthenticateOptions();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return $this->canAuthenticateOptions->usernameField;
    }

    /**
     * Know where to redirect the user after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        return $this->canAuthenticateOptions->loginRedirectPath;
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

        return redirect($this->canAuthenticateOptions->logoutRedirectPath);
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
            $this->canAuthenticateOptions->throttleMaxLoginAttempts,
            $this->canAuthenticateOptions->throttleLockoutTime
        );
    }
}