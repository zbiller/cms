<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Auth\User;
use App\Options\AuthenticateOptions;
use App\Traits\CanAuthenticate;
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
            'page' => page()->find('account')
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
                'type' => User::TYPE_FRONT,
                'verified' => User::VERIFIED_YES,
            ]);
    }
}