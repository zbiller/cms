<?php

namespace App\Models\Auth;

use App\Contracts\PermissionContract;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasAclCache;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

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
        'id',
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
     * Filter the query by type.
     *
     * @param Builder $query
     * @param int $type
     */
    public function scopeWhereType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Filter query by name.
     *
     * @param Builder $query
     * @param ...$permissions
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
     * @param int $type
     * @return Collection
     */
    public static function getGrouped($type)
    {
        return static::whereType($type)->get()->groupBy('group');
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