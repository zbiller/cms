<?php

namespace App\Models\Cms;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Options\BlockOptions;
use App\Traits\HasActivity;
use App\Traits\HasBlocks;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

class Layout extends Model
{
    use HasBlocks;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'layouts';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'identifier',
    ];

    /**
     * The constants defining the layout type.
     *
     * @const
     */
    const TYPE_DEFAULT = 1;
    const TYPE_HOME = 2;

    /**
     * The property defining the layout types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_DEFAULT => 'Default',
        self::TYPE_HOME => 'Home',
    ];

    /**
     * The options available for each layout type.
     *
     * --- label
     * The layout name displayed throughout the application.
     *
     * --- block_locations
     * The available block locations for the layout.
     * The available block locations for pages inheriting the layout.
     *
     * @var array
     */
    public static $map = [
        self::TYPE_DEFAULT => [
            'file' => 'default/default.blade.php',
            'block_locations' => [
                'header', 'content', 'footer'
            ],
        ],
        self::TYPE_HOME => [
            'file' => 'default/home.blade.php',
            'block_locations' => [
                'header', 'footer'
            ],
        ],
    ];

    /**
     * Layout has many pages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(Page::class, 'layout_id');
    }

    /**
     * Get the only layout name from the layout file.
     * Used inside blade views when working with directives.
     *
     * @return mixed
     */
    public function getBladeAttribute()
    {
        return str_replace('.blade.php', '', self::$map[$this->attributes['type']]['file']);
    }

    /**
     * Sort the query with newest records first.
     *
     * @param Builder $query
     */
    public function scopeNewest($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Filter the query by the given identifier.
     *
     * @param Builder $query
     * @param string $identifier
     */
    public function scopeWhereIdentifier($query, $identifier)
    {
        $query->where('identifier', $identifier);
    }

    /**
     * Filter the query by the given type.
     *
     * @param Builder $query
     * @param string $type
     */
    public function scopeWhereType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Filter the query by the given types.
     *
     * @param Builder $query
     * @param array $types
     */
    public function scopeWhereTypeIn($query, array $types = [])
    {
        $query->whereIn('type', $types);
    }

    /**
     * Get all block locations for the given layout (by type).
     *
     * @return array|null
     */
    public function getBlockLocations()
    {
        if (!$this->exists || !isset(Layout::$map[$this->type]['block_locations'])) {
            return null;
        }

        $locations = [];

        foreach (Layout::$map[$this->type]['block_locations'] as $index => $location) {
            $locations[] = $location;
        }

        return $locations;
    }

    /**
     * Set the options for the HasBlocks trait.
     *
     * @return BlockOptions
     */
    public static function getBlockOptions()
    {
        return BlockOptions::instance();
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