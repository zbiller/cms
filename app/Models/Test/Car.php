<?php

namespace App\Models\Test;

use App\Models\Model;
use App\Options\UrlOptions;
use App\Traits\CanSave;
use App\Traits\HasMetadata;
use App\Traits\HasUploads;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Traits\HasUrl;

class Car extends Model
{
    use HasUploads;
    use HasUrl;
    use HasMetadata;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'brand_id',
        'name',
        'slug',
        'image',
        'video',
        'audio',
        'file',
        'metadata',
    ];

    /**
     * Car belongs to Owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Car belongs to Brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Car has one Book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function book()
    {
        return $this->hasOne(Book::class, 'car_id');
    }

    /**
     * Car has many Pieces.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pieces()
    {
        return $this->hasMany(Piece::class, 'car_id');
    }

    /**
     * Car has and belongs to many Mechanics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mechanics()
    {
        return $this->belongsToMany(Mechanic::class, 'cars_mechanics_ring', 'car_id', 'mechanic_id');
    }

    /**
     * @return UrlOptions
     */
    public static function getUrlOptions()
    {
        return UrlOptions::instance()
            ->generateUrlSlugFrom('slug')
            ->saveUrlSlugTo('slug')
            ->prefixUrlWith('cars');
    }

    /**
     * @return array
     */
    public function getUploadConfig()
    {
        return [
            'images' => [
                'styles' => [
                    'image' => [
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
                    ]
                ]
            ],
            'videos' => [
                'styles' => [
                    'video' => [
                        'small' => [
                            'width' => '200',
                            'height' => '100',
                        ],
                        'big' => [
                            'width' => '600',
                            'height' => '400',
                        ],
                    ]
                ]
            ],
        ];
    }
}