<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Options\ResetPasswordOptions;
use App\Traits\CanResetPassword;

class ForgotPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * Show the application's forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('front.auth.password.forgot')->with([
            'page' => page()->find('account')
        ]);
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setAuthGuard('user')
            ->setValidator(new PasswordResetRequest)
            ->setIdentifier('email')
            ->setRedirect('/login');
    }
}