<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ResetsPasswords;
use App\Options\ResetsPasswordsOptions;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

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
     * @return ResetsPasswordsOptions
     */
    public function getResetsPasswordsOptions()
    {
        return ResetsPasswordsOptions::instance()
            ->setIdentifierField('username')
            ->setRedirectPath('/admin');
    }
}