<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanResetPassword;
use App\Options\CanResetPasswordOptions;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * @param Request $request
     * @param null $token
     * @return $this
     */
    public function show(Request $request, $token = null)
    {
        return view('admin.auth.password.reset')->with([
            'username' => $request->username,
            'token' => $token,
        ]);
    }

    /**
     * @return CanResetPasswordOptions
     */
    public function getCanResetPasswordOptions()
    {
        return CanResetPasswordOptions::instance()
            ->setIdentifierField('username')
            ->setRedirectPath('/admin');
    }
}