<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * The number of tries a user is allowed to attempt login.
     *
     * @var int
     */
    protected $maxAttempts = 3;

    /**
     * The number of minutes to delay further login attempts.
     *
     * @var int
     */
    protected $lockoutTime = 15;

    /**
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('admin.auth.login');
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts,
            $this->lockoutTime
        );
    }
}