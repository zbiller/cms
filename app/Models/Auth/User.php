<?php

namespace App\Models\Auth;

use App\Traits\HasRoles;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsVerifiable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Scopes\SelectUserScope;
use App\Scopes\JoinPersonScope;
use App\Options\ActivityOptions;
use App\Options\VerifyOptions;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
    use HasActivity;
    use IsCacheable;
    use IsVerifiable;
    use IsFilterable;
    use IsSortable;
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
        'email',
        'type',
        'verified',
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
     * The constants defining the user types.
     *
     * @const
     */
    const TYPE_ADMIN = 1;
    const TYPE_FRONT = 2;

    /**
     * The constants defining the user "verified" states.
     *
     * @const
     */
    const VERIFIED_NO = 0;
    const VERIFIED_YES = 1;

    /**
     * The constants defining the user "super" ability.
     *
     * @const
     */
    const SUPER_NO = 0;
    const SUPER_YES = 1;

    /**
     * The property defining the user "verified" states.
     *
     * @var array
     */
    public static $verified = [
        self::VERIFIED_NO => 'No',
        self::VERIFIED_YES => 'Yes',
    ];

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
     * User has many activity logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activity()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    /**
     * Sort the query with newest records first.
     *
     * @param Builder $query
     */
    public function scopeNewest($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('first_name', 'asc');
    }

    /**
     * Filter query results to show only users of the provided type.
     *
     * @param Builder $query
     * @param int $type
     */
    public function scopeType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Filter query results to show only super users.
     *
     * @param Builder $query
     */
    public function scopeSuper($query)
    {
        $query->where('super', static::SUPER_YES);
    }

    /**
     * Filter query results to show users only with the given roles.
     * Param $roles: single role type as model|string or multiple role types as a collection|array.
     *
     * @param Builder $query
     * @param array|string $roles
     */
    public function scopeWithRoles($query, $roles)
    {
        $query->role($roles);
    }

    /**
     * Filter query results to exclude users having one of the the provided username.
     *
     * @param Builder $query
     * @param ...$usernames
     */
    public function scopeNot($query, ...$usernames)
    {
        $query->whereNotIn('username', array_flatten($usernames));
    }

    /**
     * Get the user's first name.
     *
     * @return string|null
     */
    public function getFirstNameAttribute()
    {
        if (isset($this->attributes['first_name'])) {
            return $this->attributes['first_name'];
        } elseif ($this->person && $this->person->first_name) {
            return $this->person->first_name;
        }

        return null;
    }

    /**
     * Get the user's last name.
     *
     * @return string|null
     */
    public function getLastNameAttribute()
    {
        if (isset($this->attributes['last_name'])) {
            return $this->attributes['last_name'];
        } elseif ($this->person && $this->person->last_name) {
            return $this->person->last_name;
        }

        return null;
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
     * Get the user's email.
     *
     * @return string|null
     */
    public function getEmailAttribute()
    {
        if (isset($this->attributes['email'])) {
            return $this->attributes['email'];
        } elseif ($this->person && $this->person->email) {
            return $this->person->email;
        }

        return null;
    }

    /**
     * Get the user's phone.
     *
     * @return string|null
     */
    public function getPhoneAttribute()
    {
        if (isset($this->attributes['phone'])) {
            return $this->attributes['phone'];
        } elseif ($this->person && $this->person->phone) {
            return $this->person->phone;
        }

        return null;
    }

    /**
     * Determine if the current user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type == self::TYPE_ADMIN;
    }

    /**
     * Determine if the current user is a super user.
     *
     * @return bool
     */
    public function isSuper()
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
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getKeyName()};
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getTable() . '.' . $this->getKeyName();
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
        return $this->getTable() . '.' . $this->getKeyName();
    }

    /**
     * Send the password reset email.
     * Determine if user requesting the password is an admin or not.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($this, $token));
    }

    /**
     * Compose the log name.
     *
     * @param string|null $event
     * @return string
     */
    public function getLogName($event = null)
    {
        $user = auth()->check() ? auth()->user() : null;
        $name = $user && $user->exists ? $user->full_name : 'A user';

        if ($event && in_array(strtolower($event), array_map('strtolower', static::getEventsToBeLogged()->toArray()))) {
            $name .= ' ' . $event . ' a';
        } else {
            $name .= ' performed an action on a';
        }

        $name .= ' ' . strtolower(last(explode('\\', get_class($this))));

        if (!empty($this->getAttributes())) {
            if ($this->getAttribute('username')) {
                $name .= ' (' . $this->getAttribute('username') . ')';
            }
        }

        return $name;
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance();
    }

    /**
     * Set the options for the IsVerifiable trait.
     *
     * @return VerifyOptions
     */
    public static function getVerifyOptions()
    {
        return VerifyOptions::instance()
            ->shouldQueueEmailSending();
    }
}