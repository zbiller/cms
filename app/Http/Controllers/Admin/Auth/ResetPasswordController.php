<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
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
     * @return ResetPasswordOptions
     */
    public static function getResetPasswordOptions()
    {
        return ResetPasswordOptions::instance()
            ->setAuthGuard('admin')
            ->setValidator(new ResetPasswordRequest)
            ->setIdentifier('username')
            ->setRedirect(route('admin'));
    }
}