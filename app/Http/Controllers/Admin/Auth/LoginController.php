<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanAuthenticate;
use App\Options\AuthenticationOptions;

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
     * @return AuthenticationOptions
     */
    public static function getAuthenticationOptions()
    {
        return AuthenticationOptions::instance()
            ->setLoginRedirectPath('/admin')
            ->setLogoutRedirectPath('/admin/login');
    }
}