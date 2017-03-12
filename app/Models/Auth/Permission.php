<?php

namespace App\Models\Auth;

use App\Traits\RefreshesCache;
use App\Options\RefreshCacheOptions;
use App\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class Permission extends Model implements PermissionContract
{
    use RefreshesCache;

    /**
     * @var string
     */
    protected $table = 'permissions';

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
        return $this->belongsToMany(User::class, 'user_permission');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * @return Collection
     */
    public static function getGrouped()
    {
        return self::all()->groupBy('group');
    }

    /**
     * @param string $name
     * @throws ModelNotFoundException
     */
    public static function findByName($name)
    {
        try {
            return static::where('name', $name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Permission "' . $name . '" does not exist!');
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