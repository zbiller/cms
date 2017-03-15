<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanResetPassword;
use App\Options\CanResetPasswordOptions;

class ForgotPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('admin.auth.password.forgot');
    }

    /**
     * @return CanResetPasswordOptions
     */
    public function getCanResetPasswordOptions()
    {
        return CanResetPasswordOptions::instance()
            ->setRedirectPath('/admin/login');
    }
}