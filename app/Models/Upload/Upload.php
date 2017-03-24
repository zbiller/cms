<?php

namespace App\Models\Upload;

use App\Models\Model;
use Illuminate\Database\Schema\Blueprint;

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

    /**
     * Create a fully qualified upload column in a database table.
     *
     * @param string $name
     * @param Blueprint $table
     */
    public static function column($name, Blueprint $table)
    {
        $table->string($name)->nullable();
        $table->foreign($name)->references('full_path')->on(config('upload.database.table'))->onDelete('set null');
    }
}