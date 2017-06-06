<?php

/*
 * This is just a wrapper for the Laravel's SendsPasswordResetEmails and ResetsPasswords traits.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use Exception;
use ReflectionMethod;
use App\Options\ResetPasswordOptions;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

trait CanResetPassword
{
    use ResetsPasswords {
        resetPassword as baseResetPassword;
        sendResetResponse as baseSendResetResponse;
    }

    use SendsPasswordResetEmails {
        sendResetLinkResponse as baseSendResetLinkResponse;
        ResetsPasswords::broker insteadof SendsPasswordResetEmails;
    }

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\ResetPasswordOptions file.
     *
     * @var ResetPasswordOptions
     */
    protected static $resetPasswordOptions;

    /**
     * Instantiate the $CanAuthenticateOptions property with the necessary authentication properties.
     *
     * @set $CanAuthenticateOptions
     */
    public static function bootCanResetPassword()
    {
        self::checkResetPasswordOptions();

        self::$resetPasswordOptions = self::getResetPasswordOptions();

        self::validateResetPasswordOptions();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return mixed
     */
    public function guard()
    {
        return auth()->guard(self::$resetPasswordOptions->guard);
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function credentials(Request $request)
    {
        return $request->only(
            self::$resetPasswordOptions->identifier,
            'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $validator = self::$resetPasswordOptions->validator;

        $this->validate($request, $validator->rules(), $validator->messages(), $validator->attributes());

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     */
    protected function resetPassword($user, $password)
    {
        $this->baseResetPassword($user, $password);

        session()->put('password_hash_' . self::$resetPasswordOptions->guard, $user->getAuthPassword());
    }

    /**
     * Know where to redirect the user after password reset.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return self::$resetPasswordOptions->redirect;
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse($response)
    {
        session()->flash('flash_success', __($response));
        return $this->baseSendResetResponse($response);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request $request
     * @param string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        session()->flash('flash_error', __($response));
        return back();
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse($response)
    {
        if (self::$resetPasswordOptions->redirect) {
            session()->flash('flash_success', __($response));
            return redirect(self::$resetPasswordOptions->redirect);
        }

        session()->flash('flash_error', __($response));
        return $this->baseSendResetLinkResponse($response);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param Request $request
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        session()->flash('flash_error', __($response));
        return back();
    }

    /**
     * Check if mandatory password reset options have been properly set from the controller.
     * Check if $validator have been set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validateResetPasswordOptions()
    {
        if (!self::$resetPasswordOptions->validator || !(self::$resetPasswordOptions->validator instanceof FormRequest)) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanResetPassword trait.' . PHP_EOL .
                'You are required to set the "validator" form request that will validate a user registration.' . PHP_EOL .
                'You can do this from inside the getResetPasswordOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the validator must be an instance of Illuminate\Foundation\Http\FormRequest.'
            );
        }
    }

    /**
     * Verify if the getResetPasswordOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkResetPasswordOptions()
    {
        if (!method_exists(self::class, 'getResetPasswordOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getResetPasswordOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getResetPasswordOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getResetPasswordOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}