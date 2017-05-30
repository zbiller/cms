<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanResetPassword;
use App\Options\ResetPasswordOptions;

class ForgotPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        $this->setMeta('title', 'Admin - Forgot Password');

        return view('admin.auth.password.forgot');
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setRedirectPath('/admin/login');
    }
}