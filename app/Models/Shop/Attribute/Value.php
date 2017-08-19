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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Value extends Model
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
    protected $table = 'attribute_values';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attribute_id',
        'value',
    ];

    /**
     A value belongs to an attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Filter the query by the given attribute id.
     *
     * @param Builder $query
     * @param int $attribute
     */
    public function scopeWhereAttribute($query, $attribute)
    {
        $query->where('attribute_id', $attribute);
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
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Get an attribute value by it's slug
     *
     * @param string $slug
     * @return mixed
     */
    public static function findBySlug($slug)
    {
        try {
            return static::whereSlug($slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Attribute Value "' . $slug . '" does not exist!');
        }
    }

    /**
     * Get the options for the HasSlug trait.
     *
     * @return SlugOptions
     */
    public static function getSlugOptions()
    {
        return SlugOptions::instance()
            ->generateSlugFrom('value')
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
            ->logByField('value');
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