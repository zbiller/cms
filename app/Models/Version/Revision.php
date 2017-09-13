<?php

namespace App\Models\Version;

use App\Models\Auth\User;
use App\Models\Model;
use App\Traits\HasMetadata;
use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Builder;

class Revision extends Model
{
    use HasMetadata;
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'revisionable_id',
        'revisionable_type',
        'metadata',
    ];

    /**
     * Revision belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the owning revisionable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisionable()
    {
        return $this->morphTo();
    }

    /**
     * Filter the query by the given user id.
     *
     * @param Builder $query
     * @param int $id
     */
    public function scopeWhereUser($query, $id)
    {
        $query->where('user_id', $id);
    }

    /**
     * Filter the query by the given revisionable params (id, type).
     *
     * @param Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereRevisionable($query, $id, $type)
    {
        $query->where([
            'revisionable_id' => $id,
            'revisionable_type' => $type,
        ]);
    }
}