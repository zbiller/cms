<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Auth\Person;
use App\Models\Auth\User;
use App\Options\RegisterOptions;
use App\Traits\CanRegister;
use Illuminate\Http\Request;

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
            'type' => User::TYPE_FRONT,
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
        $home = page()->find('home');

        return RegisterOptions::instance()
            ->setAuthGuard('user')
            ->setValidator(new RegisterRequest)
            ->setRegisterRedirect($home->url->url)
            ->setVerificationRedirect($home->url->url);
    }
}
