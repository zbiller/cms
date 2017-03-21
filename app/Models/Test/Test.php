<?php

namespace App\Models\Test;

use App\Models\Model;
use App\Options\HasUploadsOptions;
use App\Traits\CanFilter;
use App\Traits\HasUploads;
use App\Traits\CanSort;

class Test extends Model
{
    use CanFilter, CanSort, HasUploads;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'test';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'image',
        'video',
        'file',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function testHasOne1()
    {
        return $this->hasOne(TestHasOne1::class, 'test_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function testHasOne2()
    {
        return $this->hasOne(TestHasOne2::class, 'test_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testHasMany1()
    {
        return $this->hasMany(TestHasMany1::class, 'test_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testHasMany2()
    {
        return $this->hasMany(TestHasMany2::class, 'test_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testHabtm()
    {
        return $this->belongsToMany(TestHabtm::class, 'test_test_habtm_ring', 'test_id', 'test_habtm_id');
    }



    /*public function getHasUploadsOptions()
    {
        return HasUploadsOptions::instance()
            ->setStorageDisk('uploads');

        return HasUploadsOptions::instance()
            ->setStorageDisk()
            ->setDatabaseSave()
            ->setDatabaseTable()
            ->setImageMaxSize()
            ->setImageAllowedExtensions()
            ->setImageStyles()
            ->setVideosMaxSize()
            ->setVideoAllowedExtensions()
            ->setVideoGenerateThumbnails()
            ->setVideoThumbnailsNumber()
            ->setAudioMaxSize()
            ->setAudioAllowedExtensions()
            ->setFileMaxSize()
            ->setFileAllowedExtensions();

    }*/



    public function getUploadConfig()
    {
        return [];

        return [
            'images' => [
                'styles' => [
                    'image' => [
                        'portrait' => [
                            'width' => '600',
                            'height' => '1080',
                            'ratio' => true,
                        ],
                        'landscape' => [
                            'width' => '600',
                            'height' => '200',
                            'ratio' => true,
                        ],
                        'square' => [
                            'width' => '400',
                            'height' => '400',
                            'ratio' => true,
                        ]
                    ]
                ]
            ]
        ];
    }
}