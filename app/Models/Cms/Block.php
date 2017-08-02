<?php

namespace App\Models\Cms;

use DB;
use App\Models\Model;
use App\Traits\HasUploads;
use App\Traits\HasDrafts;
use App\Traits\HasRevisions;
use App\Traits\HasDuplicates;
use App\Traits\HasActivity;
use App\Traits\HasMetadata;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\DraftOptions;
use App\Options\RevisionOptions;
use App\Options\DuplicateOptions;
use App\Options\ActivityOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use HasUploads;
    use HasDrafts;
    use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use HasMetadata;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use SoftDeletes;

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
     * List with all blocks.
     *
     * --- Label:
     * The pretty formatted block type name.
     *
     * --- Composer Class:
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
    public static $blocks = [
        'Example' => [
            'label' => 'Example Block',
            'composer_class' => 'App\Blocks\Example\Composer',
            'views_path' => 'app/Blocks/Example/Views',
            'preview_image' => 'example.jpg',
        ],
        'Features' => [
            'label' => 'Features',
            'composer_class' => 'App\Blocks\Features\Composer',
            'views_path' => 'app/Blocks/Features/Views',
            'preview_image' => 'features.jpg',
        ],
        'Team' => [
            'label' => 'Team',
            'composer_class' => 'App\Blocks\Team\Composer',
            'views_path' => 'app/Blocks/Team/Views',
            'preview_image' => 'team.jpg',
        ],
        'Copyright' => [
            'label' => 'Copyright',
            'composer_class' => 'App\Blocks\Copyright\Composer',
            'views_path' => 'app/Blocks/Copyright/Views',
            'preview_image' => 'copyright.jpg',
        ],
        'Wallpaper' => [
            'label' => 'Wallpaper',
            'composer_class' => 'App\Blocks\Wallpaper\Composer',
            'views_path' => 'app/Blocks/Wallpaper/Views',
            'preview_image' => 'wallpaper.jpg',
        ],
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleted(function (Model $model) {
            DB::table('blockables')->where('block_id', $model->id)->delete();
        });
    }

    /**
     * Get all of the records of a single entity type that are assigned to this block.
     *
     * @param string $class
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function blockables($class)
    {
        return $this->morphedByMany($class, 'blockable')->withPivot([
            'id', 'location', 'ord'
        ])->withTimestamps();
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
     * Get a list of all block locations.
     * This is done by looking inside each block's composer class -> $locations property.
     *
     * @return array
     */
    public static function getLocations()
    {
        $locations = [];

        foreach (self::$blocks as $name => $options) {
            $class = app($options['composer_class']);

            foreach ($class::$locations as $location) {
                $locations[$location] = title_case(str_replace(['_', '-'], ' ', $location));
            }
        }

        return $locations;
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: type => label.
     *
     * @return array
     */
    public static function getTypes()
    {
        $types = [];

        foreach (self::$blocks as $type => $options) {
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
    public static function getClasses()
    {
        $types = [];

        foreach (self::$blocks as $type => $options) {
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
    public static function getPaths()
    {
        $types = [];

        foreach (self::$blocks as $type => $options) {
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
    public static function getImages()
    {
        $images = [];

        foreach (self::$blocks as $type => $options) {
            $images[$type] = $options['preview_image'];
        }

        return $images;
    }

    /**
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance();
    }

    /**
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(500);
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('name');
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('name');
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
                    'metadata[footer_logo]' => [
                        'logo_default' => [
                            'width' => '85',
                            'height' => '21',
                            'ratio' => true,
                        ],
                    ],
                    'metadata[header_wallpaper]' => [
                        'wallpaper_default' => [
                            'width' => '1920',
                            'height' => '400',
                            'ratio' => true,
                        ],
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
                    'metadata[items][*][client_image]' => [
                        'default' => [
                            'width' => '203',
                            'height' => '77',
                            'ratio' => true,
                        ],
                    ],
                    'metadata[items][*][team_image]' => [
                        'desktop' => [
                            'width' => '770',
                            'height' => '860',
                            'ratio' => true,
                        ],
                        'mobile' => [
                            'width' => '385',
                            'height' => '430',
                            'ratio' => true,
                        ],
                    ],
                ]
            ],
            'videos' => [
                'styles' => [
                    'metadata[header_wallpaper]' => [
                        'wallpaper_default' => [
                            'width' => '600',
                            'height' => '400',
                            'ratio' => true,
                        ],
                    ],
                ]
            ]
        ];
    }
}