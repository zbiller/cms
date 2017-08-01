<?php

namespace App\Models\Shop;

use App\Models\Model;
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
use App\Traits\IsOrderable;
use App\Options\BlockOptions;
use App\Options\UrlOptions;
use App\Options\DraftOptions;
use App\Options\RevisionOptions;
use App\Options\DuplicateOptions;
use App\Options\OrderOptions;
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
     * Boot the model.
     *
     * Save assigned discounts for the product.
     * Save assigned taxes for the product.
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function (Product $product) {
            $attributes = request()->get('attributes');
            $discounts = request()->get('discounts');
            $taxes = request()->get('taxes');

            $product->attributes()->detach();

            if ($attributes && is_array($attributes) && !empty($attributes)) {
                ksort($attributes);

                foreach ($attributes as $data) {
                    foreach ($data as $id => $attr) {
                        if ($id && ($attribute = Attribute::find($id))) {
                            $product->attributes()->save($attribute, [
                                'ord' => isset($attr['ord']) ? (int)$attr['ord'] : 0,
                                'val' => isset($attr['val']) ? $attr['val'] : null,
                            ]);
                        }
                    }
                }
            }

            $product->discounts()->detach();

            if ($discounts && is_array($discounts) && !empty($discounts)) {
                ksort($discounts);

                foreach ($discounts as $data) {
                    foreach ($data as $id => $attributes) {
                        if ($id && ($discount = Discount::find($id))) {
                            $product->discounts()->save($discount, [
                                'ord' => isset($attributes['ord']) ? (int)$attributes['ord'] : 0
                            ]);
                        }
                    }
                }
            }

            $product->taxes()->detach();

            if ($taxes && is_array($taxes) && !empty($taxes)) {
                ksort($taxes);

                foreach ($taxes as $data) {
                    foreach ($data as $id => $attributes) {
                        if ($id && ($tax = Tax::find($id))) {
                            $product->taxes()->save($tax, [
                                'ord' => isset($attributes['ord']) ? (int)$attributes['ord'] : 0
                            ]);
                        }

                    }
                }
            }
        });
    }

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
     * Product has and belongs to many attributes.
     *
     * @param bool $ordered
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributes($ordered = true)
    {
        $query = $this->belongsToMany(Attribute::class, 'product_attribute', 'product_id', 'attribute_id')->withPivot([
            'id', 'ord', 'val'
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
     * Get the final price with discounts and taxes applied.
     *
     * @return mixed
     */
    public function getFinalPriceAttribute()
    {
        $this->attributes['price'] = $this->price_with_discounts;
        $this->attributes['price'] = $this->price_with_taxes;

        return $this->attributes['price'];
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

        foreach ($this->discounts as $discount) {
            if (!$discount->canBeApplied()) {
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

        foreach ($this->taxes as $tax) {
            if (!$tax->canBeApplied()) {
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
     * Get the product's specifications mapped into an array.
     *
     * Array format:
     *
     * [
     *     set_name =>
     *     [
     *         [
     *             "name" => attribute_name,
     *             "value" => attribute_value,
     *         ],
     *         ...
     *     ],
     *     ...
     * ]
     *
     * @return array
     */
    public function getSpecificationsAttribute()
    {
        $specifications = [];

        $attributes = $this->attributes(false)->with('set')
            ->join('sets', 'sets.id', '=', 'attributes.set_id')
            ->orderBy('sets.ord')->orderBy('product_attribute.ord');

        foreach ($attributes->get() as $attribute) {
            $set = $attribute->set;
            $pivot = $attribute->pivot;

            $specifications[$set->name][] = [
                'name' => $attribute->name,
                'value' => $pivot->val ?: $attribute->value,
            ];
        }

        return $specifications;
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
     * Filter the query by category.
     *
     * @param Builder $query
     * @param int $category
     */
    public function scopeWhereCategory($query, $category)
    {
        $query->where('category_id', $category);
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
            ->relationsToDraft('blocks', 'attributes', 'discounts', 'taxes');
    }

    /**
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(100)
            ->relationsToRevision('blocks', 'attributes', 'discounts', 'taxes');
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