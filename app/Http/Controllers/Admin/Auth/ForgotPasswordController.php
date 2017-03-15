<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ResetsPasswords;
use App\Options\ResetsPasswordsOptions;

class ForgotPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('admin.auth.password.forgot');
    }

    /**
     * @return ResetsPasswordsOptions
     */
    public function getResetsPasswordsOptions()
    {
        return ResetsPasswordsOptions::instance()
            ->setRedirectPath('/admin/login');
    }
}