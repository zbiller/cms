<?php

namespace App\Helpers;

class RelationHelper
{
    /**
     * All available Laravel's direct relations.
     *
     * @var array
     */
    protected $directRelations = [
        'Illuminate\Database\Eloquent\Relations\HasOne',
        'Illuminate\Database\Eloquent\Relations\MorphOne',
        'Illuminate\Database\Eloquent\Relations\HasMany',
        'Illuminate\Database\Eloquent\Relations\MorphMany',
        'Illuminate\Database\Eloquent\Relations\BelongsTo',
        'Illuminate\Database\Eloquent\Relations\MorphTo',
    ];

    /**
     * All available Laravel's pivoted relations.
     *
     * @var array
     */
    protected $pivotedRelations = [
        'Illuminate\Database\Eloquent\Relations\BelongsToMany',
        'Illuminate\Database\Eloquent\Relations\MorphToMany',
    ];

    /**
     * Verify if a given relation is direct or not.
     *
     * @param string $relation
     * @return bool
     */
    public function isDirect($relation)
    {
        return in_array($relation, $this->directRelations);
    }

    /**
     * Verify if a given relation is pivoted or not.
     *
     * @param string $relation
     * @return bool
     */
    public function isPivoted($relation)
    {
        return in_array($relation, $this->pivotedRelations);
    }
}