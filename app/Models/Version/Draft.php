<?php

namespace App\Models\Version;

use App\Models\Model;
use App\Models\Auth\User;
use App\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;

class Draft extends Model
{
    use HasMetadata;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'drafts';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'draftable_id',
        'draftable_type',
        'metadata',
    ];

    /**
     * The name of the "drafted at" column.
     *
     * @var string
     */
    public static $draftedAtColumn = 'drafted_at';

    /**
     * Get all of the owning draftable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function draftable()
    {
        return $this->morphTo();
    }

    /**
     * Draft belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
     * Sort the query with oldest records first.
     *
     * @param Builder $query
     */
    public function scopeOldest($query)
    {
        $query->orderBy('created_at', 'asc');
    }

    /**
     * Filter the query by the given draftable params (id, type).
     *
     * @param Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereDraftable($query, $id, $type)
    {
        $query->where([
            'draftable_id' => $id,
            'draftable_type' => $type,
        ]);
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
     * Create a fully qualified draft column in a database table.
     *
     * @param Blueprint $table
     */
    public static function column(Blueprint $table)
    {
        $table->timestamp(self::$draftedAtColumn)->nullable();
    }
}