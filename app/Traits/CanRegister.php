<?php

/*
 * This is just a wrapper for the Laravel's RegistersUsers trait.
 * Using this trait is like using the Laravel's one, but with more power of configuring options.
 * It's role is to give more flexibility in setting additional properties that are hard-coded in the framework.
 */

namespace App\Traits;

use DB;
use Crypt;
use Exception;
use ReflectionMethod;
use App\Models\Auth\User;
use App\Options\RegisterOptions;
use App\Exceptions\VerificationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

        self::validateRegisterOptions();
    }

    /**
     * Get the guard to be used during authentication.
     * If null, the default guard specified in config/auth.php will be used.
     *
     * @return mixed
     */
    public function guard()
    {
        return auth()->guard(self::$registerOptions->guard);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = self::$registerOptions->validator;
        $validator = Validator::make($request->all(), $validator->rules(), $validator->messages(), $validator->attributes());

        if ($validator->fails()) {
            session()->flash('flash_error', $validator->errors()->first());
            return back()->withErrors($validator->errors());
        }

        return DB::transaction(function () use ($request) {
            event(new Registered(
                $user = $this->create($request->all())
            ));

            if ($this->shouldVerifyEmail($user)) {
                $user->load('person');
                $user->generateVerificationToken();
                $user->sendVerificationEmail();
            } else {
                $this->guard()->login($user);
            }

            return $this->registered($request, $user) ?:
                redirect(self::$registerOptions->registerRedirect);
        });
    }

    /**
     * Handle an email verification request for the application.
     *
     * @param Request $request
     * @param string $token
     * @param string $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, $token, $email)
    {
        $token = Crypt::decrypt($token);
        $email = Crypt::decrypt($email);

        try {
            $user = User::where('email', $email)->firstOrFail();
            $user->processVerificationToken($token);

            $this->guard()->login($user);

            session()->flash('flash_success', 'Your email address has been successfully verified and you have been logged into your account!');
            return $this->verified($request, $user) ?: redirect(self::$registerOptions->verificationRedirect);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'There is no user with the provided email and token in the system!');
        } catch (VerificationException $e) {
            session()->flash('flash_error', $e->getMessage());
        }

        return redirect('/');
    }

    /**
     * Check whether or not email verification should happen after the user registration was successful.
     *
     * @param Model $model
     * @return bool
     */
    public function shouldVerifyEmail(Model $model)
    {
        return in_array(IsVerifiable::class, class_uses($model)) && self::$registerOptions->verify === true;
    }

    /**
     * Check if mandatory register options have been properly set from the controller.
     * Check if $validator has been set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validateRegisterOptions()
    {
        if (!self::$registerOptions->validator || !(self::$registerOptions->validator instanceof FormRequest)) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanRegister trait.' . PHP_EOL .
                'You are required to set the "validator" form request that will validate a user registration.' . PHP_EOL .
                'You can do this from inside the getRegisterOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the validator must be an instance of Illuminate\Foundation\Http\FormRequest.'
            );
        }
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