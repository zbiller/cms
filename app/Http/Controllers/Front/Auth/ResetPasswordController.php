<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Options\ResetPasswordOptions;
use App\Traits\CanResetPassword;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * Show the application's reset password form.
     *
     * @param Request $request
     * @param null $token
     * @return $this
     */
    public function show(Request $request, $token = null)
    {
        return view('front.auth.password.reset')->with([
            'page' => page()->find('home'),
            'username' => $request->username,
            'token' => $token,
        ]);
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        $home = page()->find('home');

        return ResetPasswordOptions::instance()
            ->setAuthGuard('user')
            ->setValidator(new PasswordResetRequest)
            ->setIdentifier('username')
            ->setRedirect($home->url->url);
    }
}