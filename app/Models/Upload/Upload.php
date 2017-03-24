<?php

namespace App\Models\Upload;

use App\Models\Model;

class Upload extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are protected against mass assign.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('upload.database.table'));
    }
}