<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateSession
{
    /**
     * The authentication factory implementation.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$request->user($guard) || !$request->session()) {
            return $next($request);
        }

        if (!$request->session()->has('password_hash_' . $guard) && $this->auth->guard($guard)->viaRemember()) {
            $this->logout($request, $guard);
        }

        if (!$request->session()->has('password_hash_' . $guard)) {
            $this->storePasswordHashInSession($request, $guard);
        }

        if ($request->session()->get('password_hash_' . $guard) !== $request->user($guard)->getAuthPassword()) {
            //dd($request->session()->all(), $guard, $request->user($guard), $this->auth->guard($guard)->viaRemember());
            $this->logout($request, $guard);
        }

        return tap($next($request), function () use ($request, $guard) {
            $this->storePasswordHashInSession($request, $guard);
        });
    }

    /**
     * Store the user's current password hash in the session.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $guard
     */
    protected function storePasswordHashInSession($request, $guard = null)
    {
        if (!$request->user($guard)) {
            return;
        }

        $request->session()->put([
            'password_hash_' . $guard => $request->user($guard)->getAuthPassword(),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $guard
     * @throws AuthenticationException
     */
    protected function logout($request, $guard = null)
    {
        $this->auth->guard($guard)->logout();

        $request->session()->flush();

        throw new AuthenticationException;
    }
}
