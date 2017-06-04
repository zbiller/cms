<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\CanResetPassword;
use App\Options\ResetPasswordOptions;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use CanResetPassword;

    /**
     * Show the application's reset password form.
     *
     * @param Request $request
     * @param null $token
     * @return $this
     */
    public function show(Request $request, $token = null)
    {
        $this->setMeta('title', 'Admin - Reset Password');

        return view('admin.auth.password.reset')->with([
            'username' => $request->username,
            'token' => $token,
        ]);
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return mixed
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    /**
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setIdentifierField('username')
            ->setRedirectPath('/admin');
    }
}