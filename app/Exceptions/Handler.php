<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use GrahamCampbell\Exceptions\NewExceptionHandler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return $this->notAuthenticated($request, $exception);
        }

        if ($exception instanceof MaintenanceModeException) {
            return $this->underMaintenance($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function notAuthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(
            in_array('admin', $exception->guards()) ? route('admin.login') : route('login')
        );
    }

    /**
     * Convert an maintenance mode exception into an maintenance mode response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Foundation\Http\Exceptions\MaintenanceModeException $exception
     * @return \Illuminate\Http\Response
     */
    protected function underMaintenance($request, MaintenanceModeException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Under Maintenance.'], 503);
        }

        if (config('app.debug') === false) {
            return response()->view('errors.503');
        }

        return parent::render($request, $exception);
    }
}
