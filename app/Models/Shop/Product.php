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
use App\Traits\HasActivity;
use App\Traits\HasMetadata;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\UrlOptions;
use App\Options\ActivityOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUploads;
    use HasBlocks;
    use HasUrl;
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
    protected $table = 'products';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'currency_id',
        'sku',
        'name',
        'slug',
        'description',
        'price',
        'quantity',
        'views',
        'sales',
        'active',
        'metadata',
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
     * Product belongs to category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Product belongs to currency.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Get all images of a product as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getImagesAttribute()
    {
        $images = collect();

        foreach ((array)$this->metadata('images') as $image) {
            $images->push($image['image']);
        }

        return $images;
    }

    /**
     * Get the first image of a product.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFirstImageAttribute()
    {
        $images = (array)$this->metadata('images');

        if (empty($images)) {
            return null;
        }

        $image = array_first($images);

        return isset($image['image']) ? $image['image'] : null;
    }

    /**
     * Get the last image of a product.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLastImageAttribute()
    {
        $images = (array)$this->metadata('images');

        if (empty($images)) {
            return null;
        }

        $image = array_last($images);

        return isset($image['image']) ? $image['image'] : null;
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
     * Set the options for the HasBlocks trait.
     *
     * @return BlockOptions
     */
    public static function getBlockOptions()
    {
        return BlockOptions::instance()
            ->setLocations(['header', 'content', 'footer'])
            ->inheritFrom('category');
    }

    /**
     * Set the options for the HasUrl trait.
     *
     * @return UrlOptions
     */
    public static function getUrlOptions()
    {
        return UrlOptions::instance()
            ->routeUrlTo('App\Http\Controllers\Front\Shop\ProductsController', 'show')
            ->generateUrlSlugFrom('slug')
            ->saveUrlSlugTo('slug')
            ->prefixUrlWith(function ($prefix, $model) {
                $prefix[] = $model->category->url->url;

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
            ->excludeColumns('created_at', 'updated_at')
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
        return [
            'images' => [
                'thumbnail_style' => [
                    'width' => 180,
                    'height' => 180
                ],
                'styles' => [
                    'metadata[images][*][image]' => [
                        'portrait' => [
                            'width' => '200',
                            'height' => '400',
                            'ratio' => true,
                        ],
                        'landscape' => [
                            'width' => '600',
                            'height' => '250',
                            'ratio' => true,
                        ],
                    ],
                ]
            ]
        ];
    }
}