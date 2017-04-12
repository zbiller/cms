<?php

/*
 * This is just a wrapper for the Laravel's SendsPasswordResetEmails and ResetsPasswords traits.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use App\Http\Requests\ResetPasswordRequest;
use App\Options\ResetPasswordOptions;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

trait CanResetPassword
{
    use ChecksTrait;

    use ResetsPasswords {
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
        self::checkOptionsMethodDeclaration('getResetPasswordOptions');

        self::$resetPasswordOptions = self::getResetPasswordOptions();
    }

    /**
     * Know where to redirect the user after password reset.
     *
     * @return string
     */
    public function redirectTo()
    {
        return self::$resetPasswordOptions->redirectPath;
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return (new ResetPasswordRequest())->rules();
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            self::$resetPasswordOptions->identifierField,
            'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse($response)
    {
        session()->flash('flash_success', trans($response));
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
        session()->flash('flash_error', trans($response));
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
        if (self::$resetPasswordOptions->redirectPath) {
            session()->flash('flash_success', trans($response));
            return redirect(self::$resetPasswordOptions->redirectPath);
        } else {
            session()->flash('flash_error', trans($response));
            return $this->baseSendResetLinkResponse($response);
        }
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
        session()->flash('flash_error', trans($response));
        return back();
    }
}