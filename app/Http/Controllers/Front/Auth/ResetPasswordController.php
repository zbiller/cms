<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanResetPassword;
use App\Options\ResetPasswordOptions;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * The name of the auth guard used.
     *
     * @var string
     */
    protected $guard = 'user';

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
     * Get the guard to be used during password reset.
     *
     * @return mixed
     */
    protected function guard()
    {
        return auth()->guard($this->guard);
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setIdentifierField('username')
            ->setRedirectPath('/');
    }
}