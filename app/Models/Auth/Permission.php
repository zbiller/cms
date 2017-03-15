<?php

namespace App\Models\Auth;

use App\Traits\Cacheable;
use App\Options\CacheableOptions;
use App\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Permission extends Model implements PermissionContract
{
    use Cacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are protected against mass assign.
     *
     * @var array
     */
    protected $guarded = [
        'id'
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
     * Get permissions as array grouped by the "group" column.
     *
     * @return Collection
     */
    public static function getGrouped()
    {
        return self::all()->groupBy('group');
    }

    /**
     * Return the permission by it's name.
     *
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
     * Set the options necessary for the Cacheable trait.
     *
     * @return CacheableOptions
     */
    public function getCacheableOptions(): CacheableOptions
    {
        return CacheableOptions::instance()
            ->setKey('acl');
    }
}