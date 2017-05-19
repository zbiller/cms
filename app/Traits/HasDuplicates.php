<?php

namespace App\Traits;

use DB;
use Relation;
use Closure;
use Exception;
use ReflectionMethod;
use App\Sniffers\ModelSniffer;
use App\Options\DuplicateOptions;
use App\Exceptions\DuplicateException;
use Illuminate\Database\Eloquent\Model;

trait HasDuplicates
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\DuplicateOptions file.
     *
     * @var DuplicateOptions
     */
    protected static $duplicateOptions;

    /**
     * Boot the trait.
     *
     * @return void
     * @throws Exception
     */
    public static function bootHasDuplicates()
    {
        self::checkDuplicateOptions();

        self::$duplicateOptions = self::getDuplicateOptions();
    }

    /**
     * Register a duplicating model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function duplicating($callback)
    {
        static::registerModelEvent('duplicating', $callback);
    }

    /**
     * Register a duplicated model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function duplicated($callback)
    {
        static::registerModelEvent('duplicated', $callback);
    }

    /**
     * Duplicate a model instance and it's relations.
     *
     * @return Model
     * @throws DuplicateException
     */
    public function saveAsDuplicate()
    {
        try {
            return DB::transaction(function () {
                if ($this->fireModelEvent('duplicating') === false) {
                    return false;
                }

                $model = $this->duplicateModel();

                if (self::$duplicateOptions->shouldDuplicateDeeply !== true) {
                    return $model;
                }

                foreach ($this->getRelationsForDuplication() as $relation => $attributes) {
                    if (Relation::isDirect($attributes['type'])) {
                        $this->duplicateDirectRelation($model, $relation);
                    }

                    if (Relation::isPivoted($attributes['type'])) {
                        $this->duplicatePivotedRelation($model, $relation);
                    }
                }

                $this->fireModelEvent('duplicated', false);

                return $model;
            });
        } catch (Exception $e) {
            throw new DuplicateException(
                'Could not duplicate the record!', $e->getCode(), $e
            );
        }
    }

    /**
     * Get a replicated instance of the original model's instance.
     *
     * @return Model
     * @throws DuplicateException
     */
    protected function duplicateModel()
    {
        try {
            $model = $this->duplicateModelWithExcluding();
            $model = $this->duplicateModelWithUnique($model);

            $model->save();

            return $model;
        } catch (Exception $e) {
            throw new DuplicateException(
                'Could not duplicate the model!', $e->getCode(), $e
            );
        }
    }

    /**
     * Duplicate a direct relation.
     * Subsequently save new relation records for the initial model instance.
     *
     * @param Model $model
     * @param string $relation
     * @return Model
     * @throws DuplicateException
     */
    protected function duplicateDirectRelation(Model $model, $relation)
    {
        try {
            $this->{$relation}()->get()->each(function ($rel) use ($model, $relation) {
                $rel = $this->duplicateRelationWithExcluding($rel, $relation);
                $rel = $this->duplicateRelationWithUnique($rel, $relation);

                $model->{$relation}()->save($rel);
            });

            return $model;
        } catch (Exception $e) {
            throw new DuplicateException(
                'Could not duplicate a direct relation!', $e->getCode(), $e
            );
        }
    }

    /**
     * Duplicate a pivoted relation.
     * Subsequently attach new pivot records corresponding to the relation for the initial model instance.
     *
     * @param Model $model
     * @param string $relation
     * @return Model
     * @throws DuplicateException
     */
    protected function duplicatePivotedRelation(Model $model, $relation)
    {
        try {
            $this->{$relation}()->get()->each(function ($rel) use ($model, $relation) {
                $attributes = $this->establishDuplicatablePivotAttributes($rel);

                $model->{$relation}()->attach($rel, $attributes);
            });

            return $model;
        } catch (Exception $e) {
            throw new DuplicateException(
                'Could not duplicate a pivoted relation!', $e->getCode(), $e
            );
        }
    }

    /**
     * Get the relations that should be duplicated alongside the original model.
     *
     * @return array
     */
    protected function getRelationsForDuplication()
    {
        $relations = [];
        $excluded = self::$duplicateOptions->excludedRelations ?: [];

        foreach ((new ModelSniffer())->getAllRelations($this) as $relation => $attributes) {
            if (!in_array($relation, $excluded)) {
                $relations[$relation] = $attributes;
            }
        }

        return $relations;
    }

    /**
     * Replicate a model instance, excluding attributes provided in the model's getDuplicateOptions() method.
     *
     * @return Model
     */
    private function duplicateModelWithExcluding()
    {
        $except = [];
        $excluded = self::$duplicateOptions->excludedColumns;

        if ($this->usesTimestamps()) {
            $except = array_merge($except, [
                $this->getCreatedAtColumn(),
                $this->getUpdatedAtColumn()
            ]);
        }

        if ($excluded && is_array($excluded) && !empty($excluded)) {
            $except = array_merge($except, $excluded);
        }

        return $this->replicate($except);
    }

    /**
     * Update a model instance.
     * With unique values for the attributes provided in the model's getDuplicateOptions() method.
     *
     * @param Model $model
     * @return Model
     */
    private function duplicateModelWithUnique(Model $model)
    {
        $unique = self::$duplicateOptions->uniqueColumns;

        if (!$unique || !is_array($unique) || empty($unique)) {
            return $model;
        }

        foreach ($unique as $i => $column) {
            $original = $value = $model->{$column};

            while (static::where($column, $value)->first()) {
                $value = $original . ' (' . ++$i . ')';

                $model->{$column} = $value;
            }
        }

        return $model;
    }

    /**
     * Replicate a model relation instance, excluding attributes provided in the model's getDuplicateOptions() method.
     *
     * @param Model $model
     * @param string $relation
     * @return Model
     */
    private function duplicateRelationWithExcluding(Model $model, $relation)
    {
        $attributes = null;
        $excluded = self::$duplicateOptions->excludedRelationColumns;

        if ($excluded && is_array($excluded) && !empty($excluded)) {
            if (array_key_exists($relation, $excluded)) {
                $attributes = $excluded[$relation];
            }
        }

        return $model->replicate($attributes);
    }

    /**
     * Update a relation for the model instance.
     * With unique values for the attributes attributes provided in the model's getDuplicateOptions() method.
     *
     * @param Model $model
     * @param string $relation
     * @return Model
     */
    private function duplicateRelationWithUnique(Model $model, $relation)
    {
        $unique = self::$duplicateOptions->uniqueRelationColumns;

        if (!$unique || !is_array($unique) || empty($unique)) {
            return $model;
        }

        if (array_key_exists($relation, $unique)) {
            foreach ($unique[$relation] as $i => $column) {
                $original = $value = $model->{$column};

                while ($model->where($column, $value)->first()) {
                    $value = $original . ' (' . ++$i . ')';

                    $model->{$column} = $value;
                }
            }
        }

        return $model;
    }

    /**
     * Get additional pivot attributes that should be saved when duplicating a pivoted relation.
     * Usually, these are attributes coming from the withPivot() method defined on the relation.
     *
     * @param Model $model
     * @return array
     */
    private function establishDuplicatablePivotAttributes(Model $model)
    {
        $pivot = $model->pivot;

        return array_except($pivot->getAttributes(), [
            $pivot->getKeyName(),
            $pivot->getForeignKey(),
            $pivot->getOtherKey(),
            $pivot->getCreatedAtColumn(),
            $pivot->getUpdatedAtColumn(),
        ]);
    }

    /**
     * Verify if the getDuplicateOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkDuplicateOptions()
    {
        if (!method_exists(self::class, 'getDuplicateOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getDuplicateOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getDuplicateOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getDuplicateOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}