<?php

namespace App\Models\Auth;

use App\Models\Model;
use App\Traits\HasPermissions;
use App\Traits\CanCache;
use App\Traits\CanFilter;
use App\Traits\CanSort;
use App\Options\CanCacheOptions;
use App\Contracts\RoleContract;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Role extends Model implements RoleContract
{
    use HasPermissions;
    use CanCache;
    use CanFilter;
    use CanSort;

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
        'type',
    ];

    /**
     * Role types.
     *
     * @const
     */
    const TYPE_NORMAL = 1;
    const TYPE_ADMIN = 2;

    /**
     * Get role types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_NORMAL => 'Normal',
        self::TYPE_ADMIN => 'Admin',
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
     * Filter query results to show roles only of type.
     * Param $types: single role type as string|int or multiple role types as an array.
     *
     * @param Builder $query
     * @param array|int|string $types
     */
    public function scopeOnly($query, $types)
    {
        $query->whereIn('type', is_array($types) ? $types : explode(',', $types));

    }

    /**
     * Filter query results to exclude the given roles.
     * Param $roles: single role as string or multiple roles as an array.
     *
     * @param Builder $query
     * @param array|string $roles
     */
    public function scopeExclude($query, $roles)
    {
        $query->whereNotIn('name', is_array($roles) ? $roles : explode(',', $roles));

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
            return static::where('name', $name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Role "' . $name . '" does not exist!');
        }
    }

    /**
     * Set the options necessary for the CanCache trait.
     *
     * @return CanCacheOptions
     */
    public function getCanCacheOptions(): CanCacheOptions
    {
        return CanCacheOptions::instance()
            ->setKey('acl');
    }
}