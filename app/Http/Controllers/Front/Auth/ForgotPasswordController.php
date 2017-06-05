<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
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
     * Get the guard to be used during password reset.
     *
     * @return mixed
     */
    protected function guard()
    {
        return auth()->guard('user');
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setRedirectPath('/login');
    }
}