<?php

namespace App\Models\Auth;

use App\Contracts\PermissionContract;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class Permission extends Model implements PermissionContract
{
    use HasActivity;
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard',
        'group',
        'label',
    ];

    /**
     * Permission has and belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }

    /**
     * Permission has and belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Filter the query by guard.
     *
     * @param Builder $query
     * @param string $guard
     */
    public function scopeWhereGuard($query, $guard)
    {
        $query->where('guard', $guard);
    }

    /**
     * Filter query by name.
     *
     * @param Builder $query
     * @param $permissions
     */
    public function scopeWhereName($query, $name)
    {
        $query->where('name', $name);
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Get permissions as array grouped by the "group" column.
     *
     * @param string $guard
     * @return Collection
     */
    public static function getGrouped($guard)
    {
        return static::whereGuard($guard)->get()->groupBy('group');
    }

    /**
     * Get a permission by it's name.
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public static function findByName($name)
    {
        try {
            return static::whereName($name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Permission "' . $name . '" does not exist!');
        }
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('name');
    }
}