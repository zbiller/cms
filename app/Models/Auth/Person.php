<?php

namespace App\Models\Auth;

use App\Models\Model;
use App\Traits\IsCacheable;

class Person extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'people';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'user',
    ];

    /**
     * Person belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}