<?php

namespace App\Models\Shop;

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
use App\Traits\SavesDiscounts;
use App\Traits\SavesTaxes;
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
    use SavesDiscounts;
    use SavesTaxes;
    use SoftDeletes;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'product_categories';

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
     * On delete verify if the category has children. If it does, don't delete the category and throw an exception.
     * After soft-deleting a category, soft-delete all its related products.
     * After restoring a category, restore all its related products.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (Category $category) {
            if ($category->children()->count() > 0) {
                throw new CrudException(
                    'Could not delete the record because it has children!'
                );
            }
        });

        static::deleted(function (Category $category) {
            $category->products()->delete();
        });

        static::restored(function (Category $category) {
            $category->products()->restore();
        });
    }

    /**
     * Category has many products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Category has and belongs to many products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relatedProducts()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'category_id', 'product_id')->withTimestamps();
    }

    /**
     * Product has and belongs to many attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute', 'category_id', 'attribute_id')->withTimestamps();
    }

    /**
     * Product has and belongs to many discounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'category_discount', 'category_id', 'discount_id')->withPivot([
            'id', 'ord'
        ])->withTimestamps()->orderBy('category_discount.ord', 'asc');
    }

    /**
     * Product has and belongs to many taxes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'category_tax', 'category_id', 'tax_id')->withPivot([
            'id', 'ord'
        ])->withTimestamps()->orderBy('category_tax.ord', 'asc');
    }

    /**
     * Get all the attributes in the filterable format.
     * This array can be used when building a "filters" visual component.
     *
     * @return array
     */
    public function getFiltersAttribute()
    {
        $filters = [];
        $attributes = Attribute::with('values')
            ->select([
                'attributes.id',
                'attributes.name',
                'attributes.slug',
                'attribute_sets.name as set_name'
            ])
            ->leftJoin('attribute_sets', 'attributes.set_id', '=', 'attribute_sets.id')
            ->leftJoin('category_attribute', 'attributes.id', '=', 'category_attribute.attribute_id')
            ->where(function ($q) {
                $q->where('attributes.filterable', Attribute::FILTERABLE_YES)
                    ->orWhere('category_attribute.category_id', $this->id);
            })
            ->orderBy('attribute_sets.ord', 'asc')
            ->orderBy('attributes.ord', 'asc')
            ->get();

        foreach ($attributes as $index => $attribute) {
            $filters[$attribute->set_name][$index] = [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'slug' => $attribute->slug,
            ];

            foreach ($attribute->values as $value) {
                $filters[$attribute->set_name][$index]['values'][] = [
                    'id' => $value->id,
                    'value' => $value->value,
                    'slug' => $value->slug,
                ];
            }
        }

        return $filters;
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
            ->relationsToDraft('blocks', 'discounts', 'taxes');
    }

    /**
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(100)
            ->relationsToRevision('blocks', 'discounts', 'taxes');
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('name', 'slug')
            ->excludeColumns('_lft', '_rgt', 'created_at', 'updated_at')
            ->excludeRelations('parent', 'children', 'url', 'products', 'drafts', 'revisions');
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