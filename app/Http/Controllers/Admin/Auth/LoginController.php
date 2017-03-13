<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Options\AuthenticatesUsersOptions;
use App\Traits\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('admin.auth.login');
    }

    /**
     * @return AuthenticatesUsersOptions
     */
    public function getAuthenticatesUsersOptions()
    {
        return AuthenticatesUsersOptions::instance()
            ->setUsernameField('username')
            ->setLoginRedirectPath('/admin')
            ->setLogoutRedirectPath('/admin/login')
            ->setThrottleMaxLoginAttempts(3)
            ->setThrottleLockoutTime(1);
    }
}