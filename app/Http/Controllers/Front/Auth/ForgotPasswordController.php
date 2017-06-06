<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Traits\CanResetPassword;
use App\Options\ResetPasswordOptions;

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
            'page' => page()->find('home')
        ]);
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setAuthGuard('user')
            ->setValidator(new ResetPasswordRequest)
            ->setIdentifier('email')
            ->setRedirect('/login');
    }
}