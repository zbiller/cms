<?php

namespace App\Models\Auth;

use App\Models\Auth\User\Address;
use App\Models\Shop\Cart;
use App\Notifications\ResetPassword;
use App\Options\ActivityOptions;
use App\Options\VerifyOptions;
use App\Scopes\WithUserPersonScope;
use App\Traits\HasActivity;
use App\Traits\HasRoles;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Traits\IsVerifiable;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

        static::addGlobalScope(new WithUserPersonScope);
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
     * User has one person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    /**
     * User has many addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
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
     * Filter query results to show only users of the provided type.
     *
     * @param Builder $query
     * @param int $type
     */
    public function scopeWhereType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Filter query results to show only super users.
     *
     * @param Builder $query
     */
    public function scopeOnlySuper($query)
    {
        $query->where('super', static::SUPER_YES);
    }

    /**
     * Filter query results to show only normal users.
     *
     * @param Builder $query
     */
    public function scopeOnlyNormal($query)
    {
        $query->where('super', static::SUPER_NO);
    }

    /**
     * Filter the query to exclude the "developer" user.
     *
     * @param Builder $query
     */
    public function scopeNotDeveloper($query)
    {
        $query->where('username', '!=', 'developer');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('first_name', 'asc');
    }

    /**
     * Get the user's first name.
     *
     * @return string|null
     */
    public function getFirstNameAttribute()
    {
        return $this->attributes['first_name'] ?? optional($this->person)->first_name;
    }

    /**
     * Get the user's last name.
     *
     * @return string|null
     */
    public function getLastNameAttribute()
    {
        return $this->attributes['last_name'] ?? optional($this->person)->last_name;
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
        return $this->attributes['email'] ?? optional($this->person)->email;
    }

    /**
     * Get the user's phone.
     *
     * @return string|null
     */
    public function getPhoneAttribute()
    {
        return $this->attributes['phone'] ?? optional($this->person)->phone;
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
     * Set the options for the IsVerifiable trait.
     *
     * @return VerifyOptions
     */
    public static function getVerifyOptions()
    {
        return VerifyOptions::instance()
            ->shouldQueueEmailSending();
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('username');
    }
}