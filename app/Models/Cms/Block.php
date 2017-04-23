<?php

namespace App\Models\Cms;

use App\Models\Model;
use App\Traits\HasUploads;
use App\Traits\HasMetadata;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

class Block extends Model
{
    use HasUploads;
    use HasMetadata;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'anchor',
        'metadata',
    ];

    /**
     * List with all of the block types.
     *
     * --- Label:
     * The pretty formatted block type name.
     *
     * --- Compose Class:
     * The full namespace to the block's view composer.
     *
     * --- Views Path:
     * The full path to the block's views directory.
     *
     * --- Preview Image:
     * The name of the image used as block type preview in admin.
     * All of the preview images should be placed inside /resources/assets/img/admin/blocks/ directory.
     * Running "gulp" is required for migrating new images to the /public directory.
     *
     * @var array
     */
    public static $map = [
        'Example' => [
            'label' => 'Example Block',
            'composer_class' => 'App\Blocks\Example\Composer',
            'views_path' => 'app/Blocks/Example/Views',
            'preview_image' => 'example.jpg',
        ],
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
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: type => label.
     *
     * @return array
     */
    public static function types()
    {
        $types = [];

        foreach (self::$map as $type => $options) {
            $types[$type] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block classes for a select.
     * Final format will be: class => label.
     *
     * @return array
     */
    public static function classes()
    {
        $types = [];

        foreach (self::$map as $type => $options) {
            $types[$options['composer_class']] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block view paths for a select.
     * Final format will be: path => label.
     *
     * @return array
     */
    public static function paths()
    {
        $types = [];

        foreach (self::$map as $type => $options) {
            $types[$options['views_class']] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: type => image.
     *
     * @return array
     */
    public static function images()
    {
        $images = [];

        foreach (self::$map as $type => $options) {
            $images[$type] = $options['preview_image'];
        }

        return $images;
    }

    /**
     * Get all of the records of a single entity type that are assigned to this block.
     *
     * @param string $class
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function related($class)
    {
        return $this->morphedByMany($class, 'blockable');
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
                'styles' => [
                    'metadata[image]' => [
                        'portrait' => [
                            'width' => '300',
                            'height' => '600',
                            'ratio' => true,
                        ],
                        'landscape' => [
                            'width' => '600',
                            'height' => '300',
                            'ratio' => true,
                        ],
                        'square' => [
                            'width' => '400',
                            'height' => '400',
                            'ratio' => true,
                        ]
                    ],
                    'metadata[items][*][image]' => [
                        'first_style' => [
                            'width' => '400',
                            'height' => '100',
                            'ratio' => true,
                        ],
                        'second_style' => [
                            'width' => '100',
                            'height' => '400',
                            'ratio' => true,
                        ],
                    ],
                ]
            ],
        ];
    }
}