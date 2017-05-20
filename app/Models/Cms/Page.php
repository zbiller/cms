<?php

namespace App\Models\Cms;

use App\Models\Model;
use App\Traits\HasBlocks;
use App\Traits\HasUrl;
use App\Traits\HasDrafts;
use App\Traits\HasRevisions;
use App\Traits\HasDuplicates;
use App\Traits\HasMetadata;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\BlockOptions;
use App\Options\UrlOptions;
use App\Options\DraftOptions;
use App\Options\RevisionOptions;
use App\Options\DuplicateOptions;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Page extends Model
{
    use HasBlocks {
        getInheritedBlocks as baseGetInheritedBlocks;
    }
    use HasUrl;
    use HasDrafts;
    use HasRevisions;
    use HasDuplicates;
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
    const TYPE_NORMAL = 1;
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
        self::TYPE_NORMAL => 'Normal',
        self::TYPE_SPECIAL => 'Special',
    ];

    /**
     * The options available for each page type.
     *
     * --- action
     * The action from the controller to be used for pages on the front-end.
     * The controller in discussion is the one defined in the routeUrlTo() method from the getUrlOptions() method.
     *
     * --- view
     * The view to be used for pages on the front-end.
     *
     * @var array
     */
    public static $map = [
        self::TYPE_NORMAL => [
            'action' => 'normal',
            'view' => 'front.cms.page',
        ],
        self::TYPE_SPECIAL => [
            'action' => 'special',
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
     * Page belongs to layout.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo(Layout::class, 'layout_id');
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
     * Get the inherited blocks for a model instance.
     * Inherited blocks can come from page or layout (recursively).
     *
     * @param string|$location
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInheritedBlocks($location)
    {
        if (!method_exists($this, 'getBlockOptions')) {
            return null;
        }

        if (static::getBlockOptions()->inherit == 'layout') {
            return $this->layout->getBlocksInLocation($location);
        }

        return $this->baseGetInheritedBlocks($location);
    }

    /**
     * Get all block locations for the given page (by layout type).
     *
     * @return array|null
     */
    public function getBlockLocations()
    {
        if (!$this->exists || !$this->layout || !isset(Layout::$map[$this->layout->type]['block_locations'])) {
            return null;
        }

        $locations = [];

        foreach (Layout::$map[$this->layout->type]['block_locations'] as $index => $location) {
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
        return BlockOptions::instance()
            ->inheritFrom('layout');
    }

    /**
     * Set the options for the HasUrl trait.
     *
     * @return UrlOptions
     */
    public static function getUrlOptions()
    {
        return UrlOptions::instance()
            ->routeUrlTo('App\Http\Controllers\Front\Cms\PagesController', 'show')
            ->generateUrlSlugFrom('slug')
            ->saveUrlSlugTo('slug')
            ->prefixUrlWith(function ($prefix, $model) {
                foreach ($model->ancestors()->get() as $parent) {
                    $prefix[] = $parent->slug;
                }

                return implode('/' , (array)$prefix);
            });
    }

    /**
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance()
            ->relationsToDraft('blocks')
            ->doNotDeletePublishedDrafts();
    }

    /**
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(100)
            ->relationsToRevision('blocks');
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->excludeColumns([
                '_lft', '_rgt', 'identifier', 'created_at', 'updated_at'
            ])
            ->excludeRelations([
                'parent', 'children', 'url', 'layout'
            ]);
    }
}