<?php

namespace App\Models\Auth;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Eager load relations by default.
     *
     * @var array
     */
    protected $with = [
        'person',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person()
    {
        return $this->hasOne(Person::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function getFirstNameAttribute()
    {
        return $this->person->first_name;
    }

    /**
     * @return mixed
     */
    public function getLastNameAttribute()
    {
        return $this->person->last_name;
    }

    /**
     * @return mixed
     */
    public function getPhoneAttribute()
    {
        return $this->person->phone;
    }

    /**
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return implode(' ', [
            $this->person->first_name,
            $this->person->last_name,
        ]);
    }

    /**
     * @param string $token
     * @param string $route
     */
    public function sendPasswordResetNotification($token)
    {
        $route = 'admin.password.change';

        $this->notify(new ResetPasswordNotification($token, $route));
    }
}