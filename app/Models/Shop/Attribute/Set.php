<?php

namespace App\Models\Shop\Attribute;

use App\Models\Model;
use App\Models\Shop\Attribute;
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

class Set extends Model
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
    protected $table = 'attribute_sets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * A set has many attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'set_id')->orderBy('ord', 'asc');
    }

    /**
     * A set has many values through the attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function values()
    {
        return $this->hasManyThrough(Value::class, Attribute::class, 'set_id', 'attribute_id');
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