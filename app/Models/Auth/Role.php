<?php

namespace App\Models\Auth;

use App\Contracts\RoleContract;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasAclCache;
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
    use HasAclCache;
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
        'type',
    ];

    /**
     * Role types.
     *
     * @const
     */
    const TYPE_ADMIN = 1;
    const TYPE_FRONT = 2;

    /**
     * Get role types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_ADMIN => 'Admin',
        self::TYPE_FRONT => 'Front',
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
     * @param ...$roles
     */
    public function scopeNot($query, ...$roles)
    {
        $query->whereNotIn('name', array_flatten($roles));

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