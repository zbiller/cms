<?php

namespace App\Http\Controllers\Admin\Auth;

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
        $this->setMeta('title', 'Admin');

        return view('admin.auth.login');
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
            ->setAuthGuard('admin')
            ->setValidator(new LoginRequest)
            ->setLoginRedirect(route('admin'))
            ->setLogoutRedirect(route('admin.login'))
            ->setAdditionalLoginConditions([
                'type' => User::TYPE_ADMIN,
                'verified' => User::VERIFIED_YES,
            ]);
    }
}