<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Options\ResetPasswordOptions;
use App\Traits\CanResetPassword;

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
        $this->setMeta('title', 'Admin - Forgot Password');

        return view('admin.auth.password.forgot');
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setAuthGuard('admin')
            ->setValidator(new PasswordResetRequest)
            ->setIdentifier('email')
            ->setRedirect(route('admin.login'));
    }
}