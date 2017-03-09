<?php

namespace App\Models\Auth;

use App\Traits\HasPermissions;
use App\Traits\RefreshesCache;
use App\Contracts\Role as RoleContract;
use App\Options\RefreshCacheOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Role extends Model implements RoleContract
{
    use HasPermissions;
    use RefreshesCache;

    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * @var array
     */
    protected $guarded = [
        'id'
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