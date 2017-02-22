<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after password reset.
     *
     * @var string
     */
    protected $redirectTo = '/admin/login';

    /**
     * @param Request $request
     * @param null $token
     * @return $this
     */
    public function show(Request $request, $token = null)
    {
        return view('admin.auth.password.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }
}