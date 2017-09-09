<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Discount extends Model
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'discounts';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'rate',
        'type',
        'for',
        'active',
        'start_date',
        'end_date',
        'min_val',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'rate' => 'float',
        'active' => 'boolean',
    ];

    /**
     * The constants defining the discount type.
     *
     * @const
     */
    const TYPE_FIXED    = 1;
    const TYPE_PERCENT  = 2;

    /**
     * The constants defining the discount usage.
     *
     * @const
     */
    const FOR_ORDER   = 1;
    const FOR_PRODUCT = 2;

    /**
     * The constants defining the discount visibility.
     *
     * @const
     */
    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;

    /**
     * The property defining the discount types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_FIXED => 'Fixed',
        self::TYPE_PERCENT => 'Percent',
    ];

    /**
     * The property defining the discount applicability.
     *
     * @var array
     */
    public static $for = [
        self::FOR_ORDER => 'Order',
        self::FOR_PRODUCT => 'Product',
    ];

    /**
     * The property defining the discount visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_NO => 'No',
        self::ACTIVE_YES => 'Yes',
    ];

    /**
     * Discount has and belongs to many products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_discount', 'discount_id', 'product_id')->withPivot([
            'id', 'ord'
        ])->withTimestamps()->orderBy('product_discount.ord', 'asc');
    }

    /**
     * Filter the query by type.
     *
     * @param Builder $query
     * @param int $type
     */
    public function scopeWhereType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Filter the query by applicability to product.
     *
     * @param Builder $query
     */
    public function scopeForProduct($query)
    {
        $query->where('for', self::FOR_PRODUCT);
    }

    /**
     * Filter the query by applicability to order.
     *
     * @param Builder $query
     */
    public function scopeForOrder($query)
    {
        $query->where('for', self::FOR_ORDER);
    }

    /**
     * Filter the query by active yes.
     *
     * @param Builder $query
     */
    public function scopeOnlyActive($query)
    {
        $query->where('active', self::ACTIVE_YES);
    }

    /**
     * Filter the query by active no.
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
     * Verify if a discount can be applied.
     * Check the discount conditions.
     *
     * @param int|null $value
     * @return bool
     */
    public function canBeApplied($value = null)
    {
        if (!$this->active) {
            return false;
        }

        if ($this->start_date && Carbon::now() < $this->start_date) {
            return false;
        }

        if ($this->end_date && Carbon::now() > $this->end_date) {
            return false;
        }

        if ($this->min_val && $this->min_val > $value) {
            return false;
        }

        return true;
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
}