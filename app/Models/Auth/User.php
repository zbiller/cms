<?php

namespace App\Models\Auth;

use App\Traits\HasRoles;
use App\Traits\CanFilter;
use App\Traits\CanSort;
use App\Scopes\SelectUserScope;
use App\Scopes\JoinPersonScope;
use App\Options\CacheOptions;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use CanFilter;
    use CanSort;

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
        'email',
    ];

    /**
     * The attributes that are excluded from json.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The relations that are eager loaded.
     *
     * @var array
     */
    protected $with = [
        'person',
    ];

    /**
     * Super user.
     *
     * @const
     */
    const SUPER_NO = 0;
    const SUPER_YES = 1;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new SelectUserScope);
        static::addGlobalScope(new JoinPersonScope);
    }

    /**
     * User has one person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person()
    {
        return $this->hasOne(Person::class, 'user_id');
    }

    /**
     * Filter query results to show users only with the given roles.
     * Param $roles: single role type as model|string or multiple role types as a collection|array.
     *
     * @param Builder $query
     * @param array|string $roles
     */
    public function scopeOnly($query, $roles)
    {
        $query->role($roles);
    }

    /**
     * Exclude the "developer" user from the query results.
     *
     * @param Builder $query
     */
    public function scopeNotDeveloper($query)
    {
        $query->where('username', '!=', 'developer');
    }

    /**
     * Get the user's full name.
     *
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    /**
     * Get the user's first name.
     *
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
     * Get the user's last name.
     *
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
     * Get the user's phone.
     *
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
     * Get the user's email.
     *
     * @return mixed
     */
    public function getEmailAttribute()
    {
        if (isset($this->attributes['email'])) {
            return $this->attributes['email'];
        } elseif ($this->person->email) {
            return $this->person->email;
        }

        return null;
    }

    /**
     * Override route model binding default column value.
     * This is done because the user is joined with person by the global scope.
     * Otherwise, the model binding will throw an "ambiguous column" error.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'users.id';
    }

    /**
     * Determine if the current user is a super user.
     *
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->super == self::SUPER_YES;
    }

    /**
     * Determine if the current user is the developer one.
     *
     * @return bool
     */
    public function isDeveloper()
    {
        return $this->username === 'developer';
    }

    /**
     * Send the password reset email.
     * Determine if user requesting the password is an admin or not.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification(
            $token, (str_contains(request()->url(), 'admin') ? 'admin.' : '') . 'password.change'
        ));
    }

    /**
     * Set the options necessary for the CanCache trait.
     *
     * @return CacheOptions
     */
    public static function getCacheOptions(): CacheOptions
    {
        return CacheOptions::instance()
            ->setKey('acl');
    }
}