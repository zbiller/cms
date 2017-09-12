<?php

namespace App\Models\Cms;

use App\Exceptions\CrudException;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Options\BlockOptions;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\RevisionOptions;
use App\Options\UrlOptions;
use App\Traits\HasActivity;
use App\Traits\HasBlocks;
use App\Traits\HasDrafts;
use App\Traits\HasDuplicates;
use App\Traits\HasMetadata;
use App\Traits\HasNodes;
use App\Traits\HasRevisions;
use App\Traits\HasUploads;
use App\Traits\HasUrl;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasUploads;
    use HasBlocks;
    use HasUrl;
    use HasNodes;
    use HasDrafts;
    use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use HasMetadata;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use SoftDeletes;

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
        'deleted_at',
        'drafted_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * The constants defining the page visibility.
     *
     * @const
     */
    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;

    /**
     * The constants defining the page type.
     *
     * @const
     */
    const TYPE_HOME = 1;
    const TYPE_DEFAULT = 2;

    /**
     * The property defining the page visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_NO => 'No',
        self::ACTIVE_YES => 'Yes',
    ];

    /**
     * The property defining the page types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_HOME => 'Home',
        self::TYPE_DEFAULT => 'Default',
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
     * The view path is relative to the /resources/views/ directory.
     *
     * --- layouts
     * The list of layout types that can be assigned to a page of that type.
     *
     * @var array
     */
    public static $map = [
        self::TYPE_HOME => [
            'action' => 'home',
            'view' => 'front.cms.pages.home',
            'layouts' => [
                Layout::TYPE_HOME,
            ],
        ],
        self::TYPE_DEFAULT => [
            'action' => 'normal',
            'view' => 'front.cms.pages.normal',
            'layouts' => [
                Layout::TYPE_DEFAULT,
                Layout::TYPE_HOME,
            ],
        ],
    ];

    /**
     * Boot the model.
     *
     * On save verify if the selected layout can be assigned to a page of the selected type.
     * On delete verify if page has children. If it does, don't delete the page and throw an exception.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Model $model) {
            if ($model->layout_id) {
                try {
                    $layout = Layout::findOrFail($model->layout_id);

                    if (!isset(static::$map[$model->type]['layouts']) || !in_array($layout->type, static::$map[$model->type]['layouts'])) {
                        throw new Exception;
                    }
                } catch (Exception $e) {
                    throw new CrudException(
                        'The layout selected does not match the page type!'
                    );
                }
            }
        });

        static::deleting(function (Model $model) {
            if ($model->children()->count() > 0) {
                throw CrudException::deletionRestrictedDueToChildren();
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
     * Get the meta title value.
     *
     * @return string|null
     */
    public function getMetaTitleAttribute()
    {
        return $this->metadata['meta']['title'] ?? null;
    }

    /**
     * Get the meta image value.
     *
     * @return string|null
     */
    public function getMetaImageAttribute()
    {
        return $this->metadata['meta']['image'] ?? null;
    }

    /**
     * Get the meta description value.
     *
     * @return string|null
     */
    public function getMetaDescriptionAttribute()
    {
        return $this->metadata['meta']['description'] ?? null;
    }

    /**
     * Get the meta keywords value.
     *
     * @return string|null
     */
    public function getMetaKeywordsAttribute()
    {
        return $this->metadata['meta']['keywords'] ?? null;
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
     * Filter the query by the given slug.
     *
     * @param Builder $query
     * @param string $slug
     */
    public function scopeWhereSlug($query, $slug)
    {
        $query->where('type', $slug);
    }

    /**
     * Filter the query to return only active results.
     *
     * @param Builder $query
     */
    public function scopeOnlyActive($query)
    {
        $query->where('active', self::ACTIVE_YES);
    }

    /**
     * Filter the query to return only inactive results.
     *
     * @param Builder $query
     */
    public function scopeOnlyInactive($query)
    {
        $query->where('active', self::ACTIVE_NO);
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
            ->relationsToDraft('blocks');
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
            ->excludeColumns('_lft', '_rgt', 'identifier', 'created_at', 'updated_at')
            ->excludeRelations('parent', 'children', 'url', 'layout', 'drafts', 'revisions');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('name');
    }

    /**
     * Get the specific upload config parts for this model.
     *
     * @return array
     */
    public function getUploadConfig()
    {
        return [];
    }
}