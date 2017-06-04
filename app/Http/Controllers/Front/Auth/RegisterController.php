<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\Person;
use App\Traits\CanRegister;
use App\Options\RegisterOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use CanRegister;

    /**
     * Show the application's register form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('front.auth.register')->with([
            'page' => page()->find('home')
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    protected function validator(array $data = [])
    {
        return Validator::make($data, [
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed',
            'person.first_name' => 'required|min:3',
            'person.last_name' => 'required|min:3',
            'person.email' => 'required|email|unique:persons,email',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */
    protected function create(array $data = [])
    {
        $user = User::create([
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
        ]);

        $person = Person::create([
            'user_id' => $user->id,
            'first_name' => isset($data['person']['first_name']) ? $data['person']['first_name'] : null,
            'last_name' => isset($data['person']['last_name']) ? $data['person']['last_name'] : null,
            'email' => isset($data['person']['email']) ? $data['person']['email'] : null,
            'phone' => isset($data['person']['phone']) ? $data['person']['phone'] : null,
        ]);

        return $user;
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return mixed
     */
    protected function guard()
    {
        return auth()->guard('user');
    }

    /**
     * The user has been registered.
     *
     * @param Request $request
     * @param User $user
     */
    public function registered(Request $request, User $user)
    {

    }

    /**
     * The user has been verified.
     *
     * @param Request $request
     * @param User $user
     */
    public function verified(Request $request, User $user)
    {

    }

    /**
     * @return RegisterOptions
     */
    public static function getRegisterOptions()
    {
        return RegisterOptions::instance()
            ->setRegisterRedirectPath('/')
            ->setVerificationRedirectPath('/');
    }
}
