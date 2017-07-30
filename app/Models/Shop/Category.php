<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Options\BlockOptions;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\RevisionOptions;
use App\Traits\HasBlocks;
use App\Traits\HasDrafts;
use App\Traits\HasDuplicates;
use App\Traits\HasRevisions;
use App\Traits\HasUploads;
use App\Traits\HasUrl;
use App\Traits\HasNodes;
use App\Traits\HasActivity;
use App\Traits\HasMetadata;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\UrlOptions;
use App\Options\ActivityOptions;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
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
    protected $table = 'categories';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'active',
        'metadata',
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
     * The constants defining the category visibility.
     *
     * @const
     */
    const ACTIVE_YES = 1;
    const ACTIVE_NO = 2;

    /**
     * The property defining the category visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_YES => 'Yes',
        self::ACTIVE_NO => 'No',
    ];

    /**
     * Boot the model.
     *
     * On delete verify if page has children. If it does, don't delete the category and throw an exception.
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
     * Set the options for the HasBlocks trait.
     *
     * @return BlockOptions
     */
    public static function getBlockOptions()
    {
        return BlockOptions::instance()
            ->setLocations(['header', 'content', 'footer'])
            ->inheritFrom(page()->find('shop'));
    }

    /**
     * Set the options for the HasUrl trait.
     *
     * @return UrlOptions
     */
    public static function getUrlOptions()
    {
        return UrlOptions::instance()
            ->routeUrlTo('App\Http\Controllers\Front\Shop\CategoriesController', 'show')
            ->generateUrlSlugFrom('slug')
            ->saveUrlSlugTo('slug')
            ->prefixUrlWith(function ($prefix, $model) {
                $prefix[] = page()->find('shop')->url->url;

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
            ->limitRevisionsTo(50)
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
            ->uniqueColumns('name')
            ->excludeColumns('_lft', '_rgt', 'created_at', 'updated_at')
            ->excludeRelations('parent', 'children', 'url', 'drafts', 'revisions');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance();
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