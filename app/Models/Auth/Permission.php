<?php

namespace App\Models\Auth;

use App\Models\Model;
use App\Traits\HasActivity;
use App\Traits\HasAclCache;
use App\Traits\IsCacheable;
use App\Options\ActivityOptions;
use App\Contracts\PermissionContract;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Permission extends Model implements PermissionContract
{
    use HasActivity;
    use HasAclCache;
    use IsCacheable;

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
     * Permission types.
     *
     * @const
     */
    const TYPE_ADMIN = 1;
    const TYPE_FRONT = 2;

    /**
     * Get permission types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_ADMIN => 'Admin',
        self::TYPE_FRONT => 'Front',
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
     * Filter query results to show permissions only of type.
     * Param $type: single role type as constant (int).
     *
     * @param Builder $query
     * @param int $type
     */
    public function scopeType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Filter query results to exclude the given roles.
     *
     * @param Builder $query
     * @param ...$permissions
     */
    public function scopeNot($query, ...$permissions)
    {
        $query->whereNotIn('name', array_flatten($permissions));
    }

    /**
     * Get permissions as array grouped by the "group" column.
     *
     * @param int $type
     * @return Collection
     */
    public static function getGrouped($type)
    {
        return self::type($type)->get()->groupBy('group');
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
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance();
    }
}