<?php

/*
 * This is just a wrapper for the Laravel's RegistersUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use DB;
use Exception;
use ReflectionMethod;
use App\Options\RegisterOptions;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

trait CanRegister
{
    use RegistersUsers;

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\RegisterOptions file.
     *
     * @var RegisterOptions
     */
    protected static $registerOptions;

    /**
     * Instantiate the $registerOptions property with the necessary registration properties.
     *
     * @set $registerOptions
     */
    public static function bootCanRegister()
    {
        self::checkRegisterOptions();

        self::$registerOptions = self::getRegisterOptions();
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            session()->flash('flash_error', $validator->errors()->first());
            return back()->withErrors($validator->errors());
        }

        return DB::transaction(function () use ($request) {
            event(new Registered(
                $user = $this->create($request->all())
            ));

            $this->guard()->login($user);

            return $this->registered($request, $user) ?: redirect($this->redirectPath());
        });
    }

    /**
     * Know where to redirect the user after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        return self::$registerOptions->redirectPath;
    }

    /**
     * Verify if the getRegisterOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkRegisterOptions()
    {
        if (!method_exists(self::class, 'getRegisterOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getRegisterOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getRegisterOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getRegisterOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}