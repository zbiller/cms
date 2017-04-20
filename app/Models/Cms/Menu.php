<?php

namespace App\Models\Cms;

use Exception;
use App\Models\Model;
use App\Traits\HasMetadata;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Builder;
use Kalnoy\Nestedset\NodeTrait;

class Menu extends Model
{
    use HasMetadata;
    use IsFilterable;
    use IsSortable;
    use NodeTrait;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menuable_id',
        'menuable_type',
        'type',
        'location',
        'name',
        'url',
        'metadata',
        'active',
    ];

    /**
     * The constants defining the menu locations.
     *
     * @const
     */
    const LOCATION_TOP = 'top';
    const LOCATION_BOTTOM = 'bottom';

    /**
     * The constants defining the entity types.
     *
     * @const
     */
    const TYPE_URL = 'url';
    const TYPE_PAGE = 'page';

    /**
     * The constants defining the menu visibility.
     *
     * @const
     */
    const ACTIVE_YES = 1;
    const ACTIVE_NO = 2;

    /**
     * The constants defining the menu tab opening.
     *
     * @const
     */
    const NEW_WINDOW_NO = 0;
    const NEW_WINDOW_YES = 1;

    /**
     * The property defining the menu locations.
     *
     * @var array
     */
    public static $locations = [
        self::LOCATION_TOP => 'Top',
        self::LOCATION_BOTTOM => 'Bottom',
    ];

    /**
     * The property defining the entity types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_URL => 'Url',
        self::TYPE_PAGE => 'Page',
    ];

    /**
     * The property defining the menu visibilities.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_YES => 'Yes',
        self::ACTIVE_NO => 'No',
    ];

    /**
     * The property defining the menu tab opening.
     *
     * @var array
     */
    public static $windows = [
        self::NEW_WINDOW_NO => 'No',
        self::NEW_WINDOW_YES => 'Yes',
    ];

    /**
     * The options available for each menu type.
     *
     * @var array
     */
    public static $map = [
        'types' => [
            self::TYPE_URL => null,
            self::TYPE_PAGE => Page::class,
        ],
    ];

    /**
     * Boot the model.
     *
     * On save pre-fill the additional data for the specified type.
     * For url type, make the relation fields null.
     * For any other type build the relation fields accordingly.
     *
     * On delete verify if menu has children.
     * If it does, don't delete the menu and throw an exception.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Model $model) {
            if (!isset($model->attributes['type'])) {
                return;
            }

            switch ($model->attributes['type']) {
                case self::TYPE_URL:
                    $model->attributes['menuable_id'] = null;
                    $model->attributes['menuable_type'] = null;

                    break;
                default:
                    if (!isset(self::$map['types'][$model->attributes['type']])) {
                        throw new Exception(
                            'Cannot create a menu entry of type "' . $model->attributes['type'] . '"' . PHP_EOL .
                            'Please make sure this type is assigned to Menu::$types and Menu::$map.'
                        );
                    }

                    $model->attributes['url'] = null;
                    $model->attributes['menuable_type'] = self::$map['types'][$model->attributes['type']];

                    break;
            }
        });

        static::deleting(function (Model $model) {
            if ($model->children()->count() > 0) {
                throw new CrudException(
                    'Could not delete the record because it has children!'
                );
            }
        });
    }

    /**
     * Get the url of the menu.
     * If the actual "url" column contains any value, return that.
     * Otherwise, match the "entity_id" and "entity_type" on a record and return it's url.
     * The matched record must implement the HasUrl trait to actually return an url.
     *
     * @return string|null
     */
    public function getUrlAttribute()
    {
        if ($this->attributes['url']) {
            return $this->attributes['url'];
        }

        @$class = $this->attributes['menuable_type'];

        if ($class && class_exists($class)) {
            try {
                $item = app($class)->findOrFail($this->attributes['menuable_id']);

                return $item->url ? $item->url->url : null;
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
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
     * Filter the query to return only results from given location.
     *
     * @param Builder $query
     * @param string $location
     */
    public function scopeWhereLocation($query, $location)
    {
        $query->where('location', $location);
    }

    /**
     * Page belongs to layout.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo(Layout::class, 'layout_id');
    }
}