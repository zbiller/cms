<?php

namespace App\Models\Auth;

use App\Traits\HasRoles;
use App\Traits\CanFilter;
use App\Traits\CanSort;
use App\Options\RefreshCacheOptions;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use CanFilter;
    use CanSort;

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
     * @const
     */
    const SUPER_NO = 0;
    const SUPER_YES = 1;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person()
    {
        return $this->hasOne(Person::class, 'user_id');
    }

    /**
     * @param Builder $query
     * @param array|string $roles
     */
    public function scopeOnly($query, $roles)
    {
        $query->role($roles);
    }

    /**
     * @param Builder $query
     */
    public function scopeNotDeveloper($query)
    {
        $query->where('username', '!=', 'developer');
    }

    /**
     * @param string $value
     */
    /*public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }*/

    /**
     * @return mixed
     */
    public function getFirstNameAttribute()
    {
        if (isset($this->attributes['first_name'])) {
            return $this->attributes['first_name'];
        } elseif ($this->person) {
            return $this->person->first_name;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getLastNameAttribute()
    {
        if (isset($this->attributes['last_name'])) {
            return $this->attributes['last_name'];
        } elseif ($this->person) {
            return $this->person->last_name;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getPhoneAttribute()
    {
        if (isset($this->attributes['phone'])) {
            return $this->attributes['phone'];
        } elseif ($this->person) {
            return $this->person->phone;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getEmailAttribute()
    {
        if (isset($this->attributes['email'])) {
            return $this->attributes['email'];
        } elseif ($this->email) {
            return $this->person->email;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    /**
     * @return bool
     */
    public function isSuper()
    {
        return $this->super == self::SUPER_YES;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->person->email;
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

    /**
     * @return RefreshCacheOptions
     */
    public function getRefreshCacheOptions(): RefreshCacheOptions
    {
        return RefreshCacheOptions::instance()
            ->setKey('acl');
    }
}