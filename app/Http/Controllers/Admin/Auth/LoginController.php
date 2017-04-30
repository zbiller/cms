<?php

namespace App\Http\Controllers\Admin\Auth;

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

        return view('admin.auth.login');
    }

    /**
     * @return AuthenticateOptions
     */
    public static function getAuthenticateOptions()
    {
        return AuthenticateOptions::instance()
            ->setLoginRedirectPath('/admin')
            ->setLogoutRedirectPath('/admin/login');
    }
}