<?php

namespace App\Models\Auth;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Model;

class Person extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'persons';

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
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            try {
                User::findOrFail($model->attributes['user_id'])->update([
                    'email' => $model->attributes['email']
                ]);
            } catch (ModelNotFoundException $e) {
                throw new Exception('There is no user for the person you\'re trying to save');
            }

            return true;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}