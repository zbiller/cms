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
    protected $table = 'values';

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
     * Filter the query by the given attribute id.
     *
     * @param Builder $query
     * @param int $id
     */
    public function scopeWhereAttribute($query, $id)
    {
        $query->where('attribute_id', $id);
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