<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Traits\HasSlug;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Traits\IsOrderable;
use App\Options\SlugOptions;
use App\Options\ActivityOptions;
use App\Options\OrderOptions;
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
    ];

    /**
     * Boot the model.
     *
     * Always eager load the values by a anonymous global scope.
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('set', function (Builder $builder) {
            $builder->with('set');
        });

        static::addGlobalScope('values', function (Builder $builder) {
            $builder->with('values');
        });
    }

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