<?php

/*
 * This is just a wrapper for the Laravel's AuthenticatesUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use App\Options\AuthenticateOptions;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use ReflectionMethod;

trait CanAuthenticate
{
    use AuthenticatesUsers;

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

        self::validateAuthenticateOptions();
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
     * Get the guard to be used during authentication.
     * If null, the default guard specified in config/auth.php will be used.
     *
     * @return mixed
     */
    public function guard()
    {
        return auth()->guard(self::$authenticationOptions->guard);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    public function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            (array)self::$authenticationOptions->additionalConditions
        );
    }

    /**
     * Log the user out of the application.
     * Forget only session data specific to the used auth guard.
     * This way, multiple auth is possible.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->forget('password_hash_' . self::$authenticationOptions->guard);

        return redirect(self::$authenticationOptions->logoutRedirect);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $validator = self::$authenticationOptions->validator;

        $this->validate($request, $validator->rules(), $validator->messages(), $validator->attributes());
    }

    /**
     * Know where to redirect the user after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        if (session()->has('intended_redirect')) {
            return session('intended_redirect');
        }

        return self::$authenticationOptions->loginRedirect;
    }

    /**
     * Set the intended url to redirect after successful login.
     *
     * @return void
     */
    protected function intendRedirectTo()
    {
        if (str_contains(url()->previous(), self::$authenticationOptions->loginRedirect)) {
            session()->flash('intended_redirect', url()->previous());
        }
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
     * Check if mandatory login options have been properly set from the controller.
     * Check if $validator has been set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validateAuthenticateOptions()
    {
        if (!self::$authenticationOptions->validator || !(self::$authenticationOptions->validator instanceof FormRequest)) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanAuthenticate trait.' . PHP_EOL .
                'You are required to set the "validator" form request that will validate a user registration.' . PHP_EOL .
                'You can do this from inside the getAuthenticateOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the validator must be an instance of Illuminate\Foundation\Http\FormRequest.'
            );
        }
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