<?php

namespace App\Models\Auth;

use App\Exceptions\ActivityException;
use App\Models\Model;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class Activity extends Model
{
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

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
        'id',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'causer',
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
        return $this->morphTo();
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
    public function scopeLikeName($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Attempt to clean old activity.
     *
     * Activity qualifies as being old if:
     * "created_at" field is smaller than the current date minus the number of days set in the
     * "delete_records_older_than" key of config/activity-log.php file.
     *
     * @return bool|void
     * @throws ActivityException
     */
    public static function clean()
    {
        $days = (int)config('activity.delete_records_older_than');

        if (!($days > 0)) {
            return;
        }

        try {
            $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');

            static::where('created_at', '<', $date)->delete();

            return true;
        } catch (Exception $e) {
            throw ActivityException::cleanupFailed();
        }
    }
}