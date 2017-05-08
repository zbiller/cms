<?php

namespace App\Traits;

use DB;
use Relation;
use Closure;
use Exception;
use ReflectionMethod;
use App\Models\Model;
use App\Models\Version\Revision;
use App\Sniffers\ModelSniffer;
use App\Options\RevisionOptions;
use App\Exceptions\RevisionException;

trait HasRevisions
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\RevisionOptions file.
     *
     * @var RevisionOptions
     */
    protected static $revisionOptions;

    /**
     * Boot the trait.
     * Remove blocks on save and delete if one or many locations from model's instance have been changed/removed.
     */
    public static function bootHasRevisions()
    {
        self::checkRevisionOptions();

        self::$revisionOptions = self::getRevisionOptions();

        static::created(function (Model $model) {
            if (self::$revisionOptions->revisionOnCreate === true) {
                $model->createNewRevision();
            }
        });

        static::updated(function (Model $model) {
            $model->createNewRevision();
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->deleteAllRevisions();
            }
        });
    }

    /**
     * Register a revisioning model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function revisioning($callback)
    {
        static::registerModelEvent('revisioning', $callback);
    }

    /**
     * Register a revisioned model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function revisioned($callback)
    {
        static::registerModelEvent('revisioned', $callback);
    }

    /**
     * Get all the revisions for a given model instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionable');
    }

    /**
     * Create a new revision record for the model instance.
     *
     * @return bool
     * @throws RevisionException
     */
    public function createNewRevision()
    {
        try {
            if ($this->fireModelEvent('revisioning') === false) {
                return false;
            }

            if (!$this->shouldCreateRevision()) {
                return false;
            }

            DB::transaction(function () {
                $this->revisions()->create([
                    'user_id' => auth()->user() ? auth()->user()->id : null,
                    'metadata' => $this->buildRevisionData(),
                ]);

                $this->clearOldRevisions();
            });

            $this->fireModelEvent('revisioned', false);

            return true;
        } catch (Exception $e) {
            throw new RevisionException(
                'Could not create a revision for the record!', $e->getCode(), $e
            );
        }
    }

    /**
     * Manually save a new revision for a model instance.
     * This method should be called manually only where and if needed.
     *
     * @return $this
     * @throws RevisionException
     */
    public function saveAsRevision()
    {
        try {
            DB::transaction(function () {
                $this->revisions()->create([
                    'user_id' => auth()->user() ? auth()->user()->id : null,
                    'metadata' => $this->buildRevisionData(),
                ]);

                $this->clearOldRevisions();
            });

            return $this;
        } catch (Exception $e) {
            throw new RevisionException(
                'Could not save the revision for the record!', $e->getCode(), $e
            );
        }
    }

    /**
     * Rollback the model instance to the given revision instance.
     *
     * @param Revision $revision
     * @return bool
     * @throws RevisionException
     */
    public function rollbackToRevision(Revision $revision)
    {
        try {
            static::revisioning(function () {
                return false;
            });

            DB::transaction(function () use ($revision) {
                $this->rollbackModelToRevision($revision);

                foreach ($revision->metadata->relations as $relation => $attributes) {
                    $this->rollbackDirectRelationToRevision($relation, $attributes);
                    $this->rollbackPivotedRelationToRevision($relation, $attributes);
                }
            });

            return true;
        } catch (Exception $e) {
            throw new RevisionException(
                'Could not rollback the record to the specified revision!', $e->getCode(), $e
            );
        }
    }

    /**
     * Remove all existing revisions from the database, belonging to a model instance.
     *
     * @throws RevisionException
     * @return void
     */
    public function deleteAllRevisions()
    {
        try {
            $this->revisions()->delete();
        } catch (Exception $e) {
            throw new RevisionException(
                'Could not delete the record\'s revisions!', $e->getCode(), $e
            );
        }
    }

    /**
     * If a revision record limit is set on the model and that limit is exceeded.
     * Remove the oldest revisions until the limit is met.
     *
     * @return void
     */
    public function clearOldRevisions()
    {
        $limit = (int)self::$revisionOptions->revisionLimit;
        $count = $this->revisions()->count();

        if ($limit > 0 && $count > $limit) {
            $this->revisions()->oldest()->take($count - $limit)->delete();
        }
    }

    /**
     * Determine if a revision should be stored for a given model instance.
     *
     * Check the revisionable fields set on the model.
     * If any of those fields have changed, then a new revisions should be stored.
     * If no fields are specifically set on the model, this will return true.
     *
     * @return bool
     */
    protected function shouldCreateRevision()
    {
        $fields = self::$revisionOptions->revisionFields;

        if ($fields && is_array($fields) && !empty($fields)) {
            return $this->isDirty($fields);
        }

        return true;
    }

    /**
     * Build the entire data array for further json insert into the revisions table.
     *
     * Extract the actual model's data.
     * Extract all of the model's direct relations data.
     * Extract all of the model's pivoted relations data.
     *
     * @return array
     */
    protected function buildRevisionData()
    {
        $data = $this->buildRevisionDataFromModel();

        foreach ($this->getRelationsForRevision() as $relation => $attributes) {
            if (Relation::isDirect($attributes['type'])) {
                $data['relations'][$relation] = $this->buildRevisionDataFromDirectRelation($relation, $attributes);
            }

            if (Relation::isPivoted($attributes['type'])) {
                $data['relations'][$relation] = $this->buildRevisionDataFromPivotedRelation($relation, $attributes);
            }
        }

        return $data;
    }

    /**
     * Get all the fields that should be revisioned from the model instance.
     * Automatically unset primary and timestamp keys.
     * Also count for revision fields if any are set on the model.
     *
     * @return array
     */
    protected function buildRevisionDataFromModel()
    {
        $data = $this->wasRecentlyCreated === true ? $this->getAttributes() : $this->getOriginal();
        $fields = self::$revisionOptions->revisionFields;

        unset($data[$this->getKeyName()]);

        if ($this->usesTimestamps()) {
            unset($data[$this->getCreatedAtColumn()]);
            unset($data[$this->getUpdatedAtColumn()]);
        }

        if ($fields && is_array($fields) && !empty($fields)) {
            foreach ($data as $field => $value) {
                if (!in_array($field, $fields)) {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    /**
     * Extract revisionable data from a model's relation.
     * Extract the type, class and related records.
     * Store the extracted data into an array to be json inserted into the revisions table.
     *
     * @param string $relation
     * @param array $attributes
     * @return array
     */
    protected function buildRevisionDataFromDirectRelation($relation, array $attributes = [])
    {
        $data = [
            'type' => $attributes['type'],
            'class' => get_class($attributes['model']),
            'records' => [
                'primary_key' => null,
                'foreign_key' => null,
                'items' => [],
            ],
        ];

        foreach ($this->{$relation}()->get() as $index => $model) {
            if (!$data['records']['primary_key'] || !$data['records']['foreign_key']) {
                $data['records']['primary_key'] = $model->getKeyName();
                $data['records']['foreign_key'] = $this->getForeignKey();
            }

            foreach ($model->getOriginal() as $field => $value) {
                if (array_key_exists($field, $model->getAttributes())) {
                    $data['records']['items'][$index][$field] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Extract revisionable data from a model's relation pivot table.
     * Extract the type, class, related records and pivot values.
     * Store the extracted data into an array to be json inserted into the revisions table.
     *
     * @param string $relation
     * @param array $attributes
     * @return array
     */
    protected function buildRevisionDataFromPivotedRelation($relation, array $attributes = [])
    {
        $data = [
            'type' => $attributes['type'],
            'class' => get_class($attributes['model']),
            'records' => [
                'primary_key' => null,
                'foreign_key' => null,
                'items' => [],
            ],
            'pivots' => [
                'primary_key' => null,
                'foreign_key' => null,
                'related_key' => null,
                'items' => [],
            ],
        ];

        foreach ($this->{$relation}()->get() as $index => $model) {
            $pivot = $model->pivot;

            foreach ($model->getOriginal() as $field => $value) {
                if (!$data['records']['primary_key'] || !$data['records']['foreign_key']) {
                    $data['records']['primary_key'] = $model->getKeyName();
                    $data['records']['foreign_key'] = $this->getForeignKey();
                }

                if (array_key_exists($field, $model->getAttributes())) {
                    $data['records']['items'][$index][$field] = $value;
                }
            }

            foreach ($pivot->getOriginal() as $field => $value) {
                if (!$data['pivots']['primary_key'] || !$data['pivots']['foreign_key'] || !$data['pivots']['related_key']) {
                    $data['pivots']['primary_key'] = $pivot->getKeyName();
                    $data['pivots']['foreign_key'] = $pivot->getForeignKey();
                    $data['pivots']['related_key'] = $pivot->getRelatedKey();
                }

                if (array_key_exists($field, $pivot->getAttributes())) {
                    $data['pivots']['items'][$index][$field] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Only rollback the model instance to the given revision.
     *
     * Loop through the revision's data.
     * If the revision's field name matches one from the model's attributes.
     * Replace the value from the model's attribute with the one from the revision.
     *
     * @param Revision $revision
     * @return void
     */
    protected function rollbackModelToRevision(Revision $revision)
    {
        foreach ($revision->metadata as $field => $value) {
            if (array_key_exists($field, $this->getAttributes())) {
                $this->{$field} = $value;
            }
        }

        $this->save();
    }

    /**
     * Only rollback the model's direct relations to the given revision.
     *
     * Loop through the stored revision's relation items.
     * If the relation exists, then update it with the data from the revision.
     * If the relation does not exist, then create a new one with the data from the revision.
     *
     * Please note that when creating a new relation, the primary key (id) will be the old one from the revision's data.
     * This way, the correspondence between the model and it's relation is kept.
     *
     * @param string $relation
     * @param object $attributes
     * @return void
     */
    protected function rollbackDirectRelationToRevision($relation, $attributes)
    {
        if (Relation::isDirect($attributes->type)) {
            foreach ($attributes->records->items as $item) {
                $rel = $this->{$relation}()->findOrNew(
                    isset($item->{$attributes->records->primary_key}) ?
                        $item->{$attributes->records->primary_key} : null
                );

                foreach ($item as $field => $value) {
                    $rel->attributes[$field] = $value;
                }

                $rel->save();
            }
        }
    }

    /**
     * Rollback a model's pivoted relations to the given revision.
     *
     * Loop through the stored revision's relation items.
     * If the relation's related model exists, then leave it as is (maybe modified) because other records or entities might be using it.
     * If the relation's related model does not exist, then create a new one with the data from the revision.
     *
     * Please note that when creating a new relation related instance, the primary key (id) will be the old one from the revision's data.
     * This way, the correspondence between the model and it's relation is kept.
     *
     * Loop through the stored revision's relation pivots.
     * Sync the model's pivot values with the ones from the revision.
     *
     * @param string $relation
     * @param object $attributes
     * @return void
     */
    protected function rollbackPivotedRelationToRevision($relation, $attributes)
    {
        if (Relation::isPivoted($attributes->type)) {
            foreach ($attributes->records->items as $item) {
                $rel = $this->{$relation}()->getRelated()->findOrNew(
                    isset($item->{$attributes->records->primary_key}) ?
                        $item->{$attributes->records->primary_key} : null
                );

                if ($rel->exists === false) {
                    foreach ($item as $field => $value) {
                        $rel->attributes[$field] = $value;
                    }

                    $rel->save();
                }
            }

            $this->{$relation}()->detach();

            foreach ($attributes->pivots->items as $item) {
                $this->{$relation}()->attach(
                    $item->{$attributes->pivots->related_key},
                    array_except((array)$item, [
                        $attributes->pivots->primary_key,
                        $attributes->pivots->foreign_key,
                        $attributes->pivots->related_key,
                    ])
                );
            }
        }
    }

    /**
     * Get the relations that should be revisionable alongside the original model.
     *
     * @return array
     */
    protected function getRelationsForRevision()
    {
        $relations = [];

        foreach ((new ModelSniffer())->getAllRelations($this) as $relation => $attributes) {
            if (in_array($relation, self::$revisionOptions->revisionRelations)) {
                $relations[$relation] = $attributes;
            }
        }

        return $relations;
    }

    /**
     * Verify if the getRevisionOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkRevisionOptions()
    {
        if (!method_exists(self::class, 'getRevisionOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getRevisionOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getRevisionOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getRevisionOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}