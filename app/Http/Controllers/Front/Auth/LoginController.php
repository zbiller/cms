<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Traits\CanAuthenticate;
use App\Options\AuthenticateOptions;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use CanAuthenticate;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $this->setIntendedRedirectUrl();

        return view('front.auth.login')->with([
            'page' => page()->find('home')
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return mixed
     */
    protected function guard()
    {
        return auth()->guard('user');
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

    }

    /**
     * @return AuthenticateOptions
     */
    public static function getAuthenticateOptions()
    {
        return AuthenticateOptions::instance()
            ->setLoginRedirectPath('/')
            ->setLogoutRedirectPath('/')
            ->setAdditionalLoginConditions([
                'type' => User::TYPE_DEFAULT,
                'verified' => User::VERIFIED_YES,
            ]);
    }
}