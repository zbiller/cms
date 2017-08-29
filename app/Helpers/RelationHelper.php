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
     * All available Laravel's direct parent relations.
     *
     * @var array
     */
    protected $parentRelations = [
        'Illuminate\Database\Eloquent\Relations\BelongsTo',
        'Illuminate\Database\Eloquent\Relations\MorphTo',
    ];

    /**
     * All available Laravel's direct child relations.
     *
     * @var array
     */
    protected $childRelations = [
        'Illuminate\Database\Eloquent\Relations\HasOne',
        'Illuminate\Database\Eloquent\Relations\MorphOne',
        'Illuminate\Database\Eloquent\Relations\HasMany',
        'Illuminate\Database\Eloquent\Relations\MorphMany',
    ];

    /**
     * All available Laravel's direct single child relations.
     *
     * @var array
     */
    protected $childRelationsSingle = [
        'Illuminate\Database\Eloquent\Relations\HasOne',
        'Illuminate\Database\Eloquent\Relations\MorphOne',
    ];

    /**
     * All available Laravel's direct multiple children relations.
     *
     * @var array
     */
    protected $childRelationsMultiple = [
        'Illuminate\Database\Eloquent\Relations\HasMany',
        'Illuminate\Database\Eloquent\Relations\MorphMany',
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

    /**
     * Verify if a given direct relation is of type parent.
     *
     * @param string $relation
     * @return bool
     */
    public function isParent($relation)
    {
        return in_array($relation, $this->parentRelations);
    }

    /**
     * Verify if a given direct relation is of type child.
     *
     * @param string $relation
     * @return bool
     */
    public function isChild($relation)
    {
        return in_array($relation, $this->childRelations);
    }

    /**
     * Verify if a given direct relation is of type single child.
     * Ex: hasOne, morphOne.
     *
     * @param string $relation
     * @return bool
     */
    public function isChildSingle($relation)
    {
        return in_array($relation, $this->childRelationsSingle);
    }

    /**
     * Verify if a given direct relation is of type single child.
     * Ex: hasMany, morphMany.
     *
     * @param string $relation
     * @return bool
     */
    public function isChildMultiple($relation)
    {
        return in_array($relation, $this->childRelationsMultiple);
    }
}