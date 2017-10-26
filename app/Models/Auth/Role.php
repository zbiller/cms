<?php

namespace App\Models\Auth;

use App\Contracts\RoleContract;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\HasPermissions;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;

class Role extends Model implements RoleContract
{
    use HasPermissions;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard',
    ];

    /**
     * Role has and belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Role has and belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Filter query results to show roles only of specific guard.
     * Param $guard: single role guard from config/auth.php -> guards (string).
     *
     * @param Builder $query
     * @param int $guard
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
     * Return the permission by it's name.
     *
     * @param string $name
     * @throws ModelNotFoundException
     * @return Role
     */
    public static function findByName($name)
    {
        try {
            return static::whereName($name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Role "' . $name . '" does not exist!');
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