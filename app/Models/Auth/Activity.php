<?php

namespace App\Models\Auth;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

class Activity extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'activity';

    /**
     * The attributes that are protected against mass assign.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * Activity belongs to a causer (user).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function causer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Activity belongs to a subject.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo()->withTrashed()->withDrafts();
    }

    /**
     * Filter the query to only include activities by a given causer.
     *
     * @param Builder $query
     * @param Model $causer
     * @return mixed
     */
    public function scopeCausedBy(Builder $query, Model $causer)
    {
        return $query->where([
            'causer_id' => $causer->getKey(),
            'causer_type' => get_class($causer)
        ]);
    }

    /**
     * Filter the query to only include activities for a given subject.
     *
     * @param Builder $query
     * @param Model $subject
     * @return mixed
     */
    public function scopeForSubject(Builder $query, Model $subject)
    {
        return $query->where([
            'causer_id' => $subject->getKey(),
            'causer_type' => get_class($subject)
        ]);
    }

    /**
     * Filter the query to return only logs containing the specified subject type.
     *
     * @param Builder $query
     * @param string $type
     * @return mixed
     */
    public function scopeWhereType($query, $type)
    {
        if ($type instanceof Model) {
            $type = get_class($type);
        }

        return $query->where('subject_type', $type);
    }

    /**
     * Filter the query to return only logs containing the search criteria.
     *
     * @param Builder $query
     * @param string $search
     * @return mixed
     */
    public function scopeLikeLog($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}