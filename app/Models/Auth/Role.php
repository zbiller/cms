<?php

namespace App\Models\Auth;

use App\Models\Model;
use App\Traits\CanFilter;
use App\Traits\CanSort;
use App\Traits\HasPermissions;
use App\Traits\RefreshesCache;
use App\Contracts\Role as RoleContract;
use App\Options\RefreshCacheOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Role extends Model implements RoleContract
{
    use HasPermissions;
    use RefreshesCache;
    use CanFilter;
    use CanSort;

    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * @param $query
     * @param array|string $roles
     */
    public function scopeExclude($query, $roles)
    {
        $query->whereNotIn('name', is_array($roles) ? $roles : explode(',', $roles));

    }

    /**
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
     * @return RefreshCacheOptions
     */
    public function getRefreshCacheOptions(): RefreshCacheOptions
    {
        return RefreshCacheOptions::instance()
            ->setKey('acl');
    }
}