<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Http\Requests\LoginRequest;
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
        $this->intendRedirectTo();

        return view('front.auth.login')->with([
            'page' => page()->find('home')
        ]);
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
        $home = page()->find('home');

        return AuthenticateOptions::instance()
            ->setAuthGuard('user')
            ->setValidator(new LoginRequest)
            ->setLoginRedirect($home->url->url)
            ->setLogoutRedirect($home->url->url)
            ->setAdditionalLoginConditions([
                'type' => User::TYPE_DEFAULT,
                'verified' => User::VERIFIED_YES,
            ]);
    }
}