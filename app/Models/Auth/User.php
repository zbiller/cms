<?php

namespace App\Models\Auth;

use App\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'email',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $with = [
        'person',
    ];

    /**
     * @var array
     */
    protected $cache = [
        'key' => 'acl'
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
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification(
            $token, (str_contains(request()->url(), 'admin') ? 'admin.' : '') . 'password.change'
        ));
    }
}