<?php

namespace App\Models\Cms;

use App\Models\Model;
use App\Traits\HasUrl;
use App\Traits\HasMetadata;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\UrlOptions;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Page extends Model
{
    use HasUrl;
    use HasMetadata;
    use IsFilterable;
    use IsSortable;
    use SoftDeletes;
    use NodeTrait;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'layout_id',
        'name',
        'slug',
        'identifier',
        'metadata',
        'canonical',
        'active',
        'type',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * The constants defining the page visibility.
     *
     * @const
     */
    const ACTIVE_YES = 1;
    const ACTIVE_NO = 2;

    /**
     * The constants defining the page type.
     *
     * @const
     */
    const TYPE_DEFAULT = 1;
    const TYPE_SPECIAL = 2;

    /**
     * The property defining the page visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_YES => 'Yes',
        self::ACTIVE_NO => 'No',
    ];

    /**
     * The property defining the page types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_DEFAULT => 'Default',
        self::TYPE_SPECIAL => 'Special',
    ];

    /**
     * The options available for each page type.
     *
     * @var array
     */
    public static $map = [
        self::TYPE_DEFAULT => [
            'controller' => 'Front\Cms\PagesController',
            'action' => 'index',
            'view' => 'front.cms.page',
        ],
        self::TYPE_SPECIAL => [
            'controller' => 'Front\Cms\PagesController',
            'action' => 'index',
            'view' => 'front.cms.page',
        ],
    ];

    /**
     * Boot the model.
     * On delete verify if page has children.
     * If it does, don't delete the page and throw an exception.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (Model $model) {
            if ($model->children()->count() > 0) {
                throw new CrudException(
                    'Could not delete the record because it has children!'
                );
            }
        });
    }

    /**
     * Get the page's forwarding for route definition.
     *
     * @return mixed
     */
    public function getRouteNameAttribute()
    {
        return isset($this->attributes['identifier']) && !empty($this->attributes['identifier']) ?
            'page.' . $this->attributes['identifier'] : null;
    }

    /**
     * Get the page's controller for route definition.
     *
     * @return mixed
     */
    public function getRouteControllerAttribute()
    {
        return self::$map[$this->attributes['type']]['controller'];
    }

    /**
     * Get the page's action for route definition.
     *
     * @return mixed
     */
    public function getRouteActionAttribute()
    {
        return self::$map[$this->attributes['type']]['action'];
    }

    /**
     * Get the page's view for route definition.
     *
     * @return mixed
     */
    public function getRouteViewAttribute()
    {
        return self::$map[$this->attributes['type']]['view'];
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
     * Filter the query to return only active results.
     *
     * @param Builder $query
     */
    public function scopeActive($query)
    {
        $query->where('active', self::ACTIVE_YES);
    }

    /**
     * Filter the query to return only inactive results.
     *
     * @param Builder $query
     */
    public function scopeInactive($query)
    {
        $query->where('active', self::ACTIVE_NO);
    }

    /**
     * Filter the query by the given parent id.
     *
     * @param Builder $query
     * @param int $id
     */
    public function scopeWhereParent($query, $id)
    {
        $query->where('parent_id', $id);
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
     * Page belongs to layout.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo(Layout::class, 'layout_id');
    }

    /**
     * @return UrlOptions
     */
    public static function getUrlOptions()
    {
        return UrlOptions::instance()
            ->generateUrlSlugFrom('slug')
            ->saveUrlSlugTo('slug')
            ->prefixUrlWith(function ($prefix, $model) {
                foreach ($model->ancestors()->get() as $parent) {
                    $prefix[] = $parent->slug;
                }

                return implode('/' , (array)$prefix);
            });
    }
}