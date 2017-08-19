<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Models\Shop\Attribute\Set;
use App\Models\Shop\Attribute\Value;
use App\Options\ActivityOptions;
use App\Options\OrderOptions;
use App\Options\SlugOptions;
use App\Traits\HasActivity;
use App\Traits\HasSlug;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsOrderable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Attribute extends Model
{
    use HasSlug;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsOrderable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'attributes';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'set_id',
        'name',
        'slug',
        'value',
        'filterable',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'set',
        'values',
    ];

    /**
     * The constants defining the attribute's filtering capabilities.
     *
     * @const
     */
    const FILTERABLE_NO = 0;
    const FILTERABLE_YES = 1;

    /**
     * The property defining the attribute's filtering capabilities.
     *
     * @var array
     */
    public static $filters = [
        self::FILTERABLE_NO => 'No',
        self::FILTERABLE_YES => 'Yes',
    ];

    /**
     * An attribute belongs to a set.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function set()
    {
        return $this->belongsTo(Set::class, 'set_id');
    }

    /**
     * An attribute has many values.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(Value::class, 'attribute_id')->orderBy('ord');
    }

    /**
     * Attribute has and belongs to many products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute', 'attribute_id', 'product_id')->withPivot([
            'id', 'ord', 'val'
        ])->withTimestamps()->orderBy('product_attribute.ord', 'asc');
    }

    /**
     * Attribute has and belongs to many categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute', 'attribute_id', 'category_id')->withTimestamps();
    }

    /**
     * Get the value (string) of the first Value object assigned to the attribute.
     *
     * @return mixed|null
     */
    public function getValueAttribute()
    {
        try {
            $value = $this->values()->firstOrFail();

            return $value->value;
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Filter the query by the given parent id.
     *
     * @param Builder $query
     * @param int $id
     */
    public function scopeWhereSet($query, $id)
    {
        $query->where('set_id', $id);
    }

    /**
     * Filter the query by slug.
     *
     * @param Builder $query
     * @param string $slug
     */
    public function scopeWhereSlug($query, $slug)
    {
        $query->where('slug', $slug);
    }

    /**
     * Filter the query to return only filterable attributes.
     *
     * @param Builder $query
     */
    public function scopeOnlyFilterable($query)
    {
        $query->where('filterable', self::FILTERABLE_YES);
    }

    /**
     * Filter the query to return only non-filterable attributes.
     *
     * @param Builder $query
     */
    public function scopeWithoutFilterable($query)
    {
        $query->where('filterable', self::FILTERABLE_NO);
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
     * Get the options for the HasSlug trait.
     *
     * @return SlugOptions
     */
    public static function getSlugOptions()
    {
        return SlugOptions::instance()
            ->generateSlugFrom('slug')
            ->saveSlugTo('slug');
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
     * Get the options for the IsOrderable trait.
     *
     * @return OrderOptions
     */
    public static function getOrderOptions()
    {
        return OrderOptions::instance();
    }
}