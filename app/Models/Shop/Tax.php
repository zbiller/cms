<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\ActivityOptions;
use Illuminate\Database\Eloquent\Builder;

class Tax extends Model
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
    protected $table = 'taxes';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'rate',
        'uses',
        'type',
        'for',
        'active',
        'start_date',
        'end_date',
        'max_val',
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
     * The constants defining the tax type.
     *
     * @const
     */
    const TYPE_FIXED    = 1;
    const TYPE_PERCENT  = 2;

    /**
     * The constants defining the tax usage.
     *
     * @const
     */
    const FOR_ORDER   = 1;
    const FOR_PRODUCT = 2;

    /**
     * The constants defining the tax visibility.
     *
     * @const
     */
    const ACTIVE_YES = 1;
    const ACTIVE_NO = 2;

    /**
     * The property defining the tax types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_FIXED => 'Fixed',
        self::TYPE_PERCENT => 'Percent',
    ];

    /**
     * The property defining the tax applicability.
     *
     * @var array
     */
    public static $for = [
        self::FOR_ORDER => 'Order',
        self::FOR_PRODUCT => 'Product',
    ];

    /**
     * The property defining the tax visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_YES => 'Yes',
        self::ACTIVE_NO => 'No',
    ];

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
     * Sort the query with oldest records first.
     *
     * @param Builder $query
     */
    public function scopeOldest($query)
    {
        $query->orderBy('created_at', 'asc');
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
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance();
    }
}