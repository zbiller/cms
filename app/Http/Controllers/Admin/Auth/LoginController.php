<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanAuthenticate;
use App\Options\CanAuthenticateOptions;

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
     * @return CanAuthenticateOptions
     */
    public function getCanAuthenticateOptions()
    {
        return CanAuthenticateOptions::instance()
            ->setUsernameField('username')
            ->setLoginRedirectPath('/admin')
            ->setLogoutRedirectPath('/admin/login')
            ->setThrottleMaxLoginAttempts(3)
            ->setThrottleLockoutTime(1);
    }
}