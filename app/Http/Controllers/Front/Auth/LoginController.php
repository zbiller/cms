<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanAuthenticate;
use App\Options\AuthenticateOptions;

class LoginController extends Controller
{
    use CanAuthenticate;

    /**
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $this->setIntendedRedirectUrl();

        return view('front.auth.login');
    }

    /**
     * @return AuthenticateOptions
     */
    public static function getAuthenticateOptions()
    {
        return AuthenticateOptions::instance()
            ->setLoginRedirectPath('/')
            ->setLogoutRedirectPath('/');
    }
}