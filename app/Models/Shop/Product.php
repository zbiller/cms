<?php

namespace App\Models\Shop;

use App\Exceptions\CartException;
use App\Models\Model;
use App\Models\Shop\Attribute\Value;
use App\Options\ActivityOptions;
use App\Options\BlockOptions;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\OrderOptions;
use App\Options\RevisionOptions;
use App\Options\UrlOptions;
use App\Traits\HasActivity;
use App\Traits\HasBlocks;
use App\Traits\HasDrafts;
use App\Traits\HasDuplicates;
use App\Traits\HasMetadata;
use App\Traits\HasRevisions;
use App\Traits\HasUploads;
use App\Traits\HasUrl;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsOrderable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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
    use IsOrderable;
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
        'content',
        'price',
        'quantity',
        'views',
        'sales',
        'active',
        'inherit_discounts',
        'inherit_taxes',
        'metadata',
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
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'currency',
    ];

    /**
     * The constants defining the product visibility.
     *
     * @const
     */
    const ACTIVE_YES = 1;
    const ACTIVE_NO = 2;

    /**
     * The constants defining the category discounts / taxes inheritance.
     *
     * @const
     */
    const INHERIT_YES = 1;
    const INHERIT_NO = 2;

    /**
     * The property defining the product visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_YES => 'Yes',
        self::ACTIVE_NO => 'No',
    ];

    /**
     * The property defining the category discounts / taxes inheritance.
     *
     * @var array
     */
    public static $inherits = [
        self::INHERIT_YES => 'Yes',
        self::INHERIT_NO => 'No',
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
     * Product has and belongs to many categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id')->withTimestamps();
    }

    /**
     * Product has and belongs to many attributes.
     *
     * @param bool $ordered
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributes($ordered = true)
    {
        $query = $this->belongsToMany(Attribute::class, 'product_attribute', 'product_id', 'attribute_id')->withPivot([
            'id', 'value_id', 'value', 'ord'
        ])->withTimestamps();

        if ($ordered === true) {
            $query->orderBy('product_attribute.ord', 'asc');
        }

        return $query;
    }

    /**
     * Product has and belongs to many discounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'product_discount', 'product_id', 'discount_id')->withPivot([
            'id', 'ord'
        ])->withTimestamps()->orderBy('product_discount.ord', 'asc');
    }

    /**
     * Product has and belongs to many taxes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'product_tax', 'product_id', 'tax_id')->withPivot([
            'id', 'ord'
        ])->withTimestamps()->orderBy('product_tax.ord', 'asc');
    }

    /**
     * Get the product's final price with discounts and taxes applied.
     * First, the discounts are applied to the price and then the taxes.
     * The discounts and taxes are applied in cascade.
     *
     * @return mixed
     */
    public function getFinalPriceAttribute()
    {
        return $this->attributes['price'] - $this->discount_value + $this->tax_value;
    }

    /**
     * Get the product's price with discounts applied.
     * Discounts are applied in cascade.
     *
     * @return float
     */
    public function getPriceWithDiscountsAttribute()
    {
        return $this->attributes['price'] - $this->discount_value;
    }

    /**
     * Get the product's price with taxes applied.
     * Taxes are applied in cascade.
     *
     * @return float
     */
    public function getPriceWithTaxesAttribute()
    {
        return $this->attributes['price'] + $this->tax_value;
    }

    /**
     * Get only the value of discounts for a product.
     * Discounts are applied in cascade.
     *
     * @return mixed
     */
    public function getDiscountValueAttribute()
    {
        $price = $this->attributes['price'];
        $discounts = $this->getNestedDiscounts();

        foreach ($discounts as $discount) {
            if (!$discount->canBeApplied($this->price)) {
                continue;
            }

            switch ($discount->type) {
                case Discount::TYPE_FIXED:
                    $price -= $discount->rate;
                    break;
                case Discount::TYPE_PERCENT:
                    $price -= ($discount->rate / 100) * $price;
                    break;
            }
        }

        return $this->attributes['price'] - $price;
    }

    /**
     * Get only the value of taxes for a product.
     * Taxes are applied in cascade.
     *
     * @return float
     */
    public function getTaxValueAttribute()
    {
        $price = $this->attributes['price'];
        $taxes = $this->getNestedTaxes();

        foreach ($taxes as $tax) {
            if (!$tax->canBeApplied($this->price)) {
                continue;
            }

            switch ($tax->type) {
                case Tax::TYPE_FIXED:
                    $price += $tax->rate;
                    break;
                case Tax::TYPE_PERCENT:
                    $price += ($tax->rate / 100) * $price;
                    break;
            }
        }

        return $price - $this->attributes['price'];
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
        if (($images = (array)$this->metadata('images')) && !empty($images)) {
            return array_first($images)['image'] ?? null;
        }

        return null;
    }

    /**
     * Get the last image of a product.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLastImageAttribute()
    {
        if (($images = (array)$this->metadata('images')) && !empty($images)) {
            return array_last($images)['image'] ?? null;
        }

        return null;
    }

    /**
     * Get the product's specifications mapped into an array.
     *
     * @return array
     */
    public function getSpecificationsAttribute()
    {
        $specifications = [];

        $attributes = $this->attributes(false)->with('set')
            ->join('attribute_sets', 'attribute_sets.id', '=', 'attributes.set_id')
            ->orderBy('attribute_sets.ord')->orderBy('product_attribute.ord');

        foreach ($attributes->get() as $attribute) {
            $set = $attribute->set;
            $pivot = $attribute->pivot;

            $specifications[$set->name][] = [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'value' => $pivot->value ?: (
                    ($value = Value::find($pivot->value_id)) ? $value->value : 'N/A'
                ),
            ];
        }

        return $specifications;
    }

    /**
     * Get the meta title value.
     *
     * @return string|null
     */
    public function getMetaTitleAttribute()
    {
        return $this->metadata->meta->title ?? null;
    }

    /**
     * Get the meta image value.
     *
     * @return string|null
     */
    public function getMetaImageAttribute()
    {
        return $this->metadata->meta->image ?? null;
    }

    /**
     * Get the meta description value.
     *
     * @return string|null
     */
    public function getMetaDescriptionAttribute()
    {
        return $this->metadata->meta->description ?? null;
    }

    /**
     * Get the meta keywords value.
     *
     * @return string|null
     */
    public function getMetaKeywordsAttribute()
    {
        return $this->metadata->meta->keywords ?? null;
    }

    /**
     * Filter the query by category id.
     *
     * @param Builder $query
     * @param int $category
     */
    public function scopeWhereCategory($query, $category)
    {
        $query->where('category_id', $category);
    }

    /**
     * Filter the query by currency id.
     *
     * @param Builder $query
     * @param int $currency
     */
    public function scopeWhereCurrency($query, $currency)
    {
        $query->where('currency_id', $currency);
    }

    /**
     * Filter the query by slug.
     *
     * @param Builder $query
     * @param int $slug
     */
    public function scopeWhereSlug($query, $slug)
    {
        $query->where('slug', $slug);
    }

    /**
     * Filter the query by price.
     *
     * @param Builder $query
     * @param float $min
     * @param float $max
     */
    public function scopeWherePriceBetween($query, $min, $max)
    {
        $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Filter the query by quantity.
     *
     * @param Builder $query
     * @param float $min
     * @param float $max
     */
    public function scopeWhereQuantityBetween($query, $min, $max)
    {
        $query->whereBetween('quantity', [$min, $max]);
    }

    /**
     * Filter the query to return only viewed results.
     *
     * @param Builder $query
     */
    public function scopeOnlyViewed($query)
    {
        $query->where('views', '>', 0);
    }

    /**
     * Filter the query to return only sold results.
     *
     * @param Builder $query
     */
    public function scopeOnlySold($query)
    {
        $query->where('sales', '>', 0);
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
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInPopularOrder($query)
    {
        $query->orderBy('views', 'desc');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInSalesOrder($query)
    {
        $query->orderBy('sales', 'desc');
    }

    /**
     * Add the product to the identified cart instance.
     *
     * @param int $quantity
     * @return $this
     * @throws CartException
     */
    public function addToCart($quantity = 1)
    {
        Cart::addProduct($this, $quantity);

        return $this;
    }

    /**
     * Remove the product from the identified cart instance.
     *
     * @return $this
     * @throws CartException
     */
    public function removeFromCart()
    {
        Cart::removeProduct($this);

        return $this;
    }

    /**
     * Update the product's quantity inside the identified cart instance.
     *
     * @param int $quantity
     * @return $this
     * @throws CartException
     */
    public function updateInCart($quantity)
    {
        Cart::updateProduct($this, $quantity);

        return $this;
    }

    /**
     * Establish if the product inherits discounts from it's main category.
     * Or from it's main category's ancestors (parents until the root).
     *
     * @return Collection
     */
    public function getInheritedDiscounts()
    {
        $category = $this->category;

        if ($category->discounts->count() > 0) {
            return $category->discounts;
        }

        foreach ($category->ancestors()->get() as $parent) {
            if ($parent->discounts->count() > 0) {
                return $parent->discounts;
            }
        }

        return collect();
    }

    /**
     * Establish if the product inherits taxes from it's main category.
     * Or from it's main category's ancestors (parents until the root).
     *
     * @return Collection
     */
    public function getInheritedTaxes()
    {
        $category = $this->category;

        if ($category->taxes->count() > 0) {
            return $category->taxes;
        }

        foreach ($category->ancestors()->get() as $parent) {
            if ($parent->taxes->count() > 0) {
                return $parent->taxes;
            }
        }

        return collect();
    }

    /**
     * Get the product's direct or inherited subsequent discounts.
     *
     * @return Collection
     */
    public function getNestedDiscounts()
    {
        $discounts = collect();

        if ($this->discounts->count() > 0) {
            $discounts = $this->discounts;
        } elseif ($this->inherit_discounts == self::INHERIT_YES) {
            if ($this->category->discounts->count() > 0) {
                $discounts = $this->category->discounts;
            }

            foreach ($this->category->ancestors as $parent) {
                if ($parent->discounts->count() > 0) {
                    $discounts = $parent->discounts;
                    break;
                }
            }
        }

        return $discounts;
    }

    /**
     * Get the product's direct or inherited subsequent taxes.
     *
     * @return Collection
     */
    public function getNestedTaxes()
    {
        $taxes = collect();

        if ($this->taxes->count() > 0) {
            $taxes = $this->taxes;
        } elseif ($this->inherit_taxes == self::INHERIT_YES) {
            if ($this->category->taxes->count() > 0) {
                $taxes = $this->category->taxes;
            }

            foreach ($this->category->ancestors as $parent) {
                if ($parent->taxes->count() > 0) {
                    $taxes = $parent->taxes;
                    break;
                }
            }
        }

        return $taxes;
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
            ->relationsToDraft('blocks', 'categories', 'attributes', 'discounts', 'taxes');
    }

    /**
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(100)
            ->relationsToRevision('blocks', 'categories', 'attributes', 'discounts', 'taxes');
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('sku', 'name', 'slug')
            ->excludeColumns('views', 'sales', 'created_at', 'updated_at')
            ->excludeRelations('url', 'category', 'currency', 'drafts', 'revisions');
    }

    /**
     * Get the options for the IsOrderable trait.
     *
     * @return OrderOptions
     */
    public static function getOrderOptions()
    {
        return OrderOptions::instance();
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
        return [
            'images' => [
                'thumbnail_style' => [
                    'width' => 180,
                    'height' => 180
                ],
                'styles' => [
                    'metadata[images][*][image]' => [
                        'thumb' => [
                            'width' => '100',
                            'height' => '100',
                            'ratio' => true,
                        ],
                        'small' => [
                            'width' => '400',
                            'height' => '400',
                            'ratio' => true,
                        ],
                        'big' => [
                            'width' => '800',
                            'height' => '600',
                            'ratio' => true,
                        ],
                    ],
                ]
            ]
        ];
    }
}