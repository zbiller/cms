<?php

namespace App\Models\Cms;

use App\Models\Model;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

class Layout extends Model
{
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'layouts';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'identifier',
        'file',
    ];

    /**
     * The layout files located in /resources/layouts/default.
     *
     * @var array
     */
    public static $files = [];

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
     * Filter the query by the given identifier.
     *
     * @param Builder $query
     * @param string $identifier
     */
    public function scopeIdentify($query, $identifier)
    {
        $query->where('identifier', $identifier);
    }

    /**
     * Layout has many pages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(Page::class, 'layout_id');
    }

    /**
     * Search for all layout files located in /resources/layouts/default.
     * Get them pretty formatted as an array.
     *
     * @return array
     */
    public static function getFiles()
    {
        foreach (glob(resource_path('layouts/default/*.blade.php')) as $layout) {
            $file = last(explode('/', $layout));

            if (!starts_with($file, '_')) {
                self::$files[$file] = $file;
            }
        }

        if (empty(self::$files)) {
            self::$files[null] = 'You have no layout files';
        }

        return self::$files;
    }
}