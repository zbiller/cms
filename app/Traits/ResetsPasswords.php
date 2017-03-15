<?php

/*
 * This is just a wrapper for the Laravel's SendsPasswordResetEmails and ResetsPasswords traits.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use App\Http\Requests\ResetPasswordRequest;
use App\Options\ResetsPasswordsOptions;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords as ResetsPasswordsBase;
use Illuminate\Http\Request;

trait ResetsPasswords
{
    use ResetsPasswordsBase {
        sendResetResponse as baseSendResetResponse;
    }

    use SendsPasswordResetEmails {
        sendResetLinkResponse as baseSendResetLinkResponse;
        ResetsPasswordsBase::broker insteadof SendsPasswordResetEmails;
    }

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\ResetsPasswordsOptions file.
     *
     * @var ResetsPasswordsOptions
     */
    protected $resetsPasswordsOptions;

    /**
     * The method used for setting the authentication options.
     * This method should be called inside the controller using this trait.
     * Inside the method, you should set all the authentication options.
     * This can be achieved using the methods from App\Options\AuthenticatesUsersOptions.
     *
     * @return ResetsPasswordsOptions
     */
    abstract public function getResetsPasswordsOptions(): ResetsPasswordsOptions;

    /**
     * Instantiate the $authenticatesUsersOptions property with the necessary authentication properties.
     *
     * @set $authenticatesUsersOptions
     */
    public function bootResetsPasswords()
    {
        $this->resetsPasswordsOptions = $this->getResetsPasswordsOptions();
    }

    /**
     * Know where to redirect the user after password reset.
     *
     * @return string
     */
    public function redirectTo()
    {
        return $this->resetsPasswordsOptions->redirectPath;
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
            $this->resetsPasswordsOptions->identifierField,
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
        if ($this->resetsPasswordsOptions->redirectPath) {
            session()->flash('flash_success', trans($response));
            return redirect($this->resetsPasswordsOptions->redirectPath);
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