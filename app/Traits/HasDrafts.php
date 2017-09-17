<?php

namespace App\Traits;

use App\Exceptions\DraftException;
use App\Models\Model;
use App\Models\Version\Draft;
use App\Options\DraftOptions;
use App\Scopes\DraftingScope;
use App\Sniffers\ModelSniffer;
use BadMethodCallException;
use Closure;
use DB;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionMethod;

trait HasDrafts
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\DraftOptions file.
     *
     * @var DraftOptions
     */
    protected static $draftOptions;

    /**
     * Boot the trait.
     * Remove blocks on save and delete if one or many locations from model's instance have been changed/removed.
     */
    public static function bootHasDrafts()
    {
        self::checkDraftOptions();

        self::$draftOptions = self::getDraftOptions();

        static::addGlobalScope(new DraftingScope);

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->deleteAllDrafts();
            }
        });
    }

    /**
     * Register a drafting model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function drafting($callback)
    {
        static::registerModelEvent('drafting', $callback);
    }

    /**
     * Register a drafted model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function drafted($callback)
    {
        static::registerModelEvent('drafted', $callback);
    }

    /**
     * Get all the drafts for a given model instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function drafts()
    {
        return $this->morphMany(Draft::class, 'draftable');
    }

    /**
     * Save the the model instance as a new draft record.
     *
     * If the model does not exist or it was just recently created, save a limbo draft.
     * If the model exists and and it was not just recently created, save a regular draft.
     *
     * @param array $data
     * @param Draft $draft
     * @return bool
     * @throws DraftException
     */
    public function saveAsDraft(array $data = [], Draft $draft = null)
    {
        try {
            if ($this->fireModelEvent('drafting') === false) {
                return false;
            }

            $model = DB::transaction(function () use ($data, $draft) {
                $data = $draft && $draft->exists ?
                    $this->mergeDraftModelData($data, $draft) :
                    $this->mergeOriginalModelData($data);

                if ($this->isLimboDraft()) {
                    $model = $this->saveLimboDraft($data);
                } else {
                    $model = $this->saveRegularDraft($data, $draft);
                }

                return $model;
            });

            $this->isLimboDraft() ?
                $model->fireModelEvent('drafted', true) :
                $this->fireModelEvent('drafted', true);

            return $model;
        } catch (Exception $e) {
            throw DraftException::saveFailed();
        }
    }

    /**
     * Publish a model instance and it's relations to the given draft version.
     *
     * If no draft is provided as parameter, it means that a "limbo" draft should be published.
     * If a draft instance is provided as parameter, it means that a "regular" draft should be published.
     *
     * If revisioning is enabled from inside the getDraftOptions() method.
     * A new revision containing the model's attributes and relation from before publishing will be stored.
     *
     * If deletion of published drafts is enabled from inside the getDraftOptions() method.
     * The just published draft will be deleted from database.
     *
     * @param Draft|null $draft
     * @return bool
     * @throws DraftException
     */
    public function publishDraft(Draft $draft = null)
    {
        try {
            $model = DB::transaction(function () use ($draft) {
                if ($draft && $draft->exists) {
                    $model = $this->publishRegularDraft($draft);
                } else {
                    $model = $this->publishLimboDraft();
                }

                return $model;
            });

            return $model;
        } catch (Exception $e) {
            throw DraftException::publishFailed();
        }
    }

    /**
     * Delete a draft.
     * If the $id parameter is specified, the script will try to delete a record from the "drafts" table.
     * Otherwise, the script will perform a "limbo" delete, meaning that it will force delete the original entity record.
     *
     * @param int|null $id
     * @return void
     * @throws DraftException
     */
    public function deleteDraft($id = null)
    {
        try {
            if ($id && ($draft = Draft::find($id))) {
                $draft->delete();

                return;
            }

            $this->deleteLimboDraft();
        } catch (Exception $e) {
            throw DraftException::deleteFailed();
        }
    }

    /**
     * Remove all existing drafts from the database, belonging to a model instance.
     *
     * @throws DraftException
     * @return void
     */
    public function deleteAllDrafts()
    {
        try {
            $this->drafts()->delete();
        } catch (Exception $e) {
            throw DraftException::deleteFailed();
        }
    }

    /**
     * Get the name of the "drafted at" column.
     *
     * @return string
     */
    public function getDraftedAtColumn()
    {
        return Draft::$draftedAtColumn;
    }

    /**
     * Get the fully qualified "drafted at" column.
     *
     * @return string
     */
    public function getQualifiedDraftedAtColumn()
    {
        return $this->getTable() . '.' . $this->getDraftedAtColumn();
    }

    /**
     * Establish if the current draft action is to be applied on a limbo or regular draft.
     *
     * @return bool
     */
    protected function isLimboDraft()
    {
        return $this->wasRecentlyCreated || !$this->exists || !is_null($this->{$this->getDraftedAtColumn()});
    }

    /**
     * Determine if a revision should be created for the model instance.
     * When an existing draft is published.
     *
     * @return bool
     */
    protected function shouldCreateRevisionForDraft()
    {
        return
            self::$draftOptions->createRevisionWhenPublishingDraft === true &&
            array_key_exists(HasRevisions::class, class_uses($this));
    }

    /**
     * Determine if the just published draft should be deleted from database.
     *
     * @return bool
     */
    protected function shouldDeletePublishedDraft()
    {
        return self::$draftOptions->deletePublishedDraft === true;
    }

    /**
     * Save a "limbo" draft.
     *
     * @param array $data
     * @return Model
     */
    protected function saveLimboDraft(array $data = [])
    {
        if ($this->exists) {
            $model = $this;
            $model->update($data);
        } else {
            $model = $this->create($data);
        }

        $this->newQueryWithoutScopes()->where($model->getKeyName(), $model->getKey())->update([
            $this->getDraftedAtColumn() => $this->fromDateTime($this->freshTimestamp())
        ]);

        $this->saveRelationsForLimboDraft($model, $data);

        return $model;
    }

    /**
     * Save a "regular" draft.
     * If the $draft parameter is supplied, updating an existing draft will be attempted.
     *
     * @param array $data
     * @param Draft $draft
     * @return Model
     */
    protected function saveRegularDraft(array $data = [], Draft $draft = null)
    {
        $attributes = [
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'metadata' => $this->buildDraftData($data, $draft),
        ];

        if ($draft && $draft->exists) {
            $draft->update($attributes, ['touch' => false]);
        } else {
            $draft = $this->drafts()->create($attributes);
        }

        return $draft;
    }

    /**
     * Publish a "limbo" draft.
     *
     * @return Model
     */
    protected function publishLimboDraft()
    {
        $this->{$this->getDraftedAtColumn()} = null;
        $this->save();

        return $this->fresh();
    }

    /**
     * Publish a "regular" draft.
     *
     * @param Draft $draft
     * @return Model
     */
    protected function publishRegularDraft(Draft $draft)
    {
        if ($this->shouldCreateRevisionForDraft()) {
            $this->saveAsRevision();
        }

        $this->publishModelFromDraft($draft);

        if (isset($draft->metadata['relations'])) {
            foreach ($draft->metadata['relations'] as $relation => $attributes) {
                if (relation()->isDirect($attributes['type'])) {
                    $this->publishDirectRelationFromDraft($relation, $attributes);
                }

                if (relation()->isPivoted($attributes['type'])) {
                    $this->publishPivotedRelationFromDraft($relation, $attributes);
                }
            }
        }

        if ($this->shouldDeletePublishedDraft()) {
            $draft->delete();
        }

        return $this->fresh();
    }

    /**
     * Delete a "limbo" draft.
     * Meaning: delete the actual original loaded entity type
     *
     * @return void
     */
    protected function deleteLimboDraft()
    {
        if (!$this->exists || is_null($this->{$this->getDraftedAtColumn()})) {
            return;
        }


        if (array_key_exists(SoftDeletes::class, class_uses($this))) {
            $this->forceDelete();
        } else {
            $this->delete();
        }
    }

    /**
     * Save all relations for a limbo draft.
     *
     * @param Model $model
     * @param array $data
     * @throws DraftException
     */
    protected function saveRelationsForLimboDraft(Model $model, array $data = [])
    {
        try {
            foreach ($this->getRelationsForDraft() as $relation => $attributes) {
                if (!isset($data[$relation]) || empty($data[$relation]) || self::$draftOptions->softDraftRelations === true) {
                    continue;
                }

                if (relation()->isDirect($attributes['type']) && relation()->isChild($attributes['type'])) {
                    if (relation()->isChildSingle($attributes['type'])) {
                        $this->saveSingleChildRelationForLimboDraft($model, $relation, $data);
                    }

                    if (relation()->isChildMultiple($attributes['type'])) {
                        $this->saveMultipleChildrenRelationForLimboDraft($model, $relation, $data);
                    }
                }

                if (relation()->isPivoted($attributes['type'])) {
                    $this->savePivotedRelationForLimboDraft($model, $relation, $data);
                }
            }
        } catch (Exception $e) {
            throw DraftException::saveRelationFailed();
        }
    }

    /**
     * Save a direct relation for the limbo draft of type "single child".
     * Ex: hasOne, morphOne.
     *
     * @param Model $model
     * @param string $relation
     * @param array $data
     */
    protected function saveSingleChildRelationForLimboDraft(Model $model, $relation, array $data = [])
    {
        if ($model->{$relation} && $model->{$relation}->exists) {
            $model->{$relation}->update($data[$relation]);
        } else {
            $model->{$relation}()->create($data[$relation]);
        }
    }

    /**
     * Save a direct relation for the limbo draft of type "multiple children".
     * Ex: hasMany, morphMany.
     *
     * @param Model $model
     * @param string $relation
     * @param array $data
     */
    protected function saveMultipleChildrenRelationForLimboDraft(Model $model, $relation, array $data = [])
    {
        foreach ($data[$relation] as $attributes) {
            $primary = $model->{$relation}()->getRelated()->getKeyName();

            if (array_key_exists($primary, $attributes)) {
                $related = $model->{$relation}()->find($attributes[$primary]);

                if ($related && $related->exists) {
                    $related->update(array_except($attributes, $primary));
                }
            } else {
                $model->{$relation}()->create($attributes);
            }
        }
    }

    /**
     * Save a pivoted relation for the limbo draft.
     *
     * @param Model $model
     * @param string $relation
     * @param array $data
     */
    protected function savePivotedRelationForLimboDraft(Model $model, $relation, array $data = [])
    {
        $model->{$relation}()->detach();

        foreach ($data[$relation] as $index => $parameters) {
            if (is_array($parameters)) {
                if (array_depth($parameters) > 1) {
                    foreach ($parameters as $id => $attributes) {
                        $this->attachPivotedRelationFromExistingOrNewRelated(
                            $model, $id, $relation, $attributes
                        );
                    }
                } else {
                    $this->attachPivotedRelationFromExistingOrNewRelated(
                        $model, $index, $relation, $parameters
                    );
                }
            } else {
                $this->attachPivotedRelationFromExistingOrNewRelated(
                    $model, $parameters, $relation, []
                );
            }
        }
    }

    /**
     * Merge the existing model's json data that's not included with the request.
     * Meaning, the already existing (ignored) data should still be saved.
     *
     * @param array $data
     * @return array
     */
    protected function mergeOriginalModelData(array $data = [])
    {
        if (!$this->exists) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if ($this->isJsonCastable($key)) {
                $data[$key] = array_merge(array_except(
                    $this->fromJson($this->attributes[$key]), $key
                ), $data[$key]);
            }
        }

        return $data;
    }

    /**
     * Merge the existing model's json data that's not included with the request.
     * Meaning, the already existing (ignored) data should still be saved.
     *
     * @param array $data
     * @param Draft $draft
     * @return array
     */
    protected function mergeDraftModelData(array $data = [], Draft $draft)
    {
        return array_replace_recursive($draft->metadata, $data);
    }

    /**
     * Build the entire data array for further json insert into the drafts table.
     *
     * @param array $params
     * @param Draft $draft
     * @return array
     */
    protected function buildDraftData(array $params = [], Draft $draft = null)
    {
        $data = $this->buildDraftDataFromModel($params);

        foreach ($this->getRelationsForDraft() as $relation => $attributes) {
            if ((!isset($params[$relation]) || empty($params[$relation])) && self::$draftOptions->softDraftRelations === true) {
                continue;
            }

            if (relation()->isDirect($attributes['type'])) {
                $data['relations'][$relation] = $this->buildDraftDataFromDirectRelation(
                    $params, $relation, $attributes
                );
            }

            if (relation()->isPivoted($attributes['type'])) {
                $data['relations'][$relation] = $this->buildDraftDataFromPivotedRelation(
                    $params, $relation, $attributes, $draft
                );
            }
        }

        return $data;
    }

    /**
     * Get all the fields that should be drafted from the model instance.
     * Automatically unset primary and timestamp keys.
     * Also count for draft fields if any are set on the model.
     *
     * @param array $params
     * @return array
     */
    protected function buildDraftDataFromModel(array $params = [])
    {
        $data = [];
        $fields = self::$draftOptions->draftFields;

        foreach ($params as $field => $value) {
            if (array_key_exists($field, $this->getAttributes())) {
                $data[$field] = $value;
            }
        }

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
     * Extract draftable data from a model's relation.
     * Extract the type, class and related records.
     * Store the extracted data into an array to be json inserted into the drafts table.
     *
     * @param array $params
     * @param string $relation
     * @param array $attributes
     * @return array
     */
    protected function buildDraftDataFromDirectRelation(array $params = [], $relation, array $attributes = [])
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

        if (isset($params[$relation]) && !empty($params[$relation])) {
            $data['records']['primary_key'] = last(explode('.', $this->{$relation}()->getQualifiedParentKeyName()));

            try {
                $data['records']['foreign_key'] = last(explode('.', $this->{$relation}()->getQualifiedForeignKey()));
            } catch (BadMethodCallException $e) {
                $data['records']['foreign_key'] = last(explode('.', $this->{$relation}()->getQualifiedForeignKeyName()));
            }

            foreach ($params[$relation] as $index => $values) {
                if (is_string($values)) {
                    $data['records']['items'][0][$index] = $values;
                } elseif (is_array($values)) {
                    foreach ($values as $field => $value) {
                        $data['records']['items'][$index][$field] = $value;
                    }
                }
            }

            foreach ($data['records']['items'] as $index => $item) {
                if (!isset($item[$data['records']['foreign_key']])) {
                    $data['records']['items'][$index][$data['records']['foreign_key']] = $this->getKey();
                }
            }
        }

        return $data;
    }

    /**
     * Extract draftable data from a model's relation pivot table.
     * Extract the type, class, related records and pivot values.
     * Store the extracted data into an array to be json inserted into the drafts table.
     *
     * @param array $params
     * @param string $relation
     * @param array $attributes
     * @param Draft $draft
     * @return array
     */
    protected function buildDraftDataFromPivotedRelation(array $params = [], $relation, array $attributes = [], Draft $draft = null)
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

        if (isset($params[$relation]) && !empty($params[$relation])) {
            DB::beginTransaction();

            $this->{$relation}()->detach();

            foreach ($params[$relation] as $index => $parameters) {
                if (is_array($parameters)) {
                    if (array_depth($parameters) > 1) {
                        foreach ($parameters as $id => $attributes) {
                            $this->attachPivotedRelationFromExistingOrNewRelated(
                                $this, $id, $relation, $attributes, $draft
                            );
                        }
                    } else {
                        $this->attachPivotedRelationFromExistingOrNewRelated(
                            $this, $index, $relation, $parameters, $draft
                        );
                    }
                } else {
                    $this->attachPivotedRelationFromExistingOrNewRelated(
                        $this, $parameters, $relation, [], $draft
                    );
                }
            }

            $relations = $this->{$relation}()->get();

            DB::rollBack();

            if ($relations->count() > 0) {
                foreach ($relations as $index => $model) {
                    $pivot = $model->pivot;

                    if (!$data['records']['primary_key'] || !$data['records']['foreign_key']) {
                        $data['records']['primary_key'] = $model->getKeyName();
                        $data['records']['foreign_key'] = $this->getForeignKey();
                    }

                    if (!$data['pivots']['primary_key'] || !$data['pivots']['foreign_key'] || !$data['pivots']['related_key']) {
                        $data['pivots']['primary_key'] = $pivot->getKeyName();
                        $data['pivots']['foreign_key'] = $pivot->getForeignKey();
                        $data['pivots']['related_key'] = $pivot->getRelatedKey();
                    }

                    $data['records']['items'][$index] = $model->getAttributes();
                    $data['pivots']['items'][$index] = $pivot->getAttributes();
                }
            }
        }

        return $data;
    }

    /**
     * Only publish the model instance to the given draft version.
     *
     * Loop through the draft's data.
     * If the draft's field name matches one from the model's attributes.
     * Replace the value from the model's attribute with the one from the draft.
     *
     * @param Draft $draft
     * @return void
     */
    protected function publishModelFromDraft(Draft $draft)
    {
        foreach ($draft->metadata as $field => $value) {
            if (array_key_exists($field, $this->getAttributes())) {
                parent::setAttribute($field, $value);
            }
        }

        $this->save();
    }

    /**
     * Only publish the model's direct relations to the given draft version.
     *
     * Loop through the stored draft's relation items.
     * If the relation exists, then update it with the data from the draft.
     * If the relation does not exist, then create a new one with the data from the draft.
     *
     * Please note that when creating a new relation, the primary key (id) will be the old one from the draft's data.
     * This way, the correspondence between the model and it's relation is kept.
     *
     * @param string $relation
     * @param object $attributes
     */
    protected function publishDirectRelationFromDraft($relation, $attributes)
    {
        if (relation()->isParent($attributes['type'])) {
            $this->publishDirectParentRelationFromDraft($relation, $attributes);
        } elseif (relation()->isChild($attributes['type'])) {
            $this->publishDirectChildRelationFromDraft($relation, $attributes);
        }
    }

    /**
     * Publish a direct parent relation to the given draft version.
     * Example: belongs to
     *
     * @param string $relation
     * @param object $attributes
     */
    protected function publishDirectParentRelationFromDraft($relation, $attributes)
    {
        foreach ($attributes['records']['items'] as $item) {
            $related = $this->{$relation}();

            if (array_key_exists(SoftDeletes::class, class_uses($this->{$relation}))) {
                $related = $related->withTrashed();
            }

            $rel = $related->findOrNew($item[$attributes['records']['primary_key']] ?? null);

            foreach ($item as $field => $value) {
                if ($field != $attributes['records']['primary_key']) {
                    $rel->attributes[$field] = $value;
                }
            }

            if (array_key_exists(SoftDeletes::class, class_uses($rel))) {
                $rel->{$rel->getDeletedAtColumn()} = null;
            }

            $rel->save();
        }
    }

    /**
     * Publish a direct child relation to the given draft version.
     * Example: has one, has many
     *
     * @param string $relation
     * @param object $attributes
     */
    protected function publishDirectChildRelationFromDraft($relation, $attributes)
    {
        $related = $this->{$relation}()->getRelated();

        if (array_key_exists(SoftDeletes::class, class_uses($related))) {
            $this->{$relation}()->forceDelete();
        } else {
            $this->{$relation}()->delete();
        }

        foreach ($attributes->records->items as $item) {
            $rel = $related->newInstance();

            foreach ($item as $field => $value) {
                if ($field != $attributes['records']['primary_key']) {
                    $rel->attributes[$field] = $value;
                }
            }

            $rel->save();
        }
    }

    /**
     * Publish a model's pivoted relations to the given draft version.
     *
     * Loop through the stored draft's relation items.
     * If the relation's related model exists, then leave it as is (maybe modified) because other records or entities might be using it.
     * If the relation's related model does not exist, then create a new one with the data from the draft.
     *
     * Please note that when creating a new relation related instance, the primary key (id) will be the old one from the draft's data.
     * This way, the correspondence between the model and it's relation is kept.
     *
     * Loop through the stored draft's relation pivots.
     * Sync the model's pivot values with the ones from the draft.
     *
     * @param string $relation
     * @param object $attributes
     */
    protected function publishPivotedRelationFromDraft($relation, $attributes)
    {
        foreach ($attributes['records']['items'] as $item) {
            $related = $this->{$relation}()->getRelated();

            if (array_key_exists(SoftDeletes::class, class_uses($related))) {
                $related = $related->withTrashed();
            }

            $rel = $related->findOrNew($item[$attributes['records']['primary_key']] ?? null);

            if ($rel->exists === false) {
                foreach ((array)$item as $field => $value) {
                    $rel->attributes[$field] = $value;
                }

                $rel->save();
            } elseif (array_key_exists(SoftDeletes::class, class_uses($rel))) {
                $rel->{$rel->getDeletedAtColumn()} = null;
                $rel->save();
            }
        }

        $this->{$relation}()->detach();

        foreach ($attributes['pivots']['items'] as $item) {
            $this->{$relation}()->attach(
                $item[$attributes['pivots']['related_key']],
                array_except((array)$item, [
                    $attributes['pivots']['primary_key'],
                    $attributes['pivots']['foreign_key'],
                    $attributes['pivots']['related_key'],
                ])
            );
        }
    }

    /**
     * Get the relations that should be draftable alongside the original model.
     *
     * @return array
     */
    protected function getRelationsForDraft()
    {
        $relations = [];

        foreach ((new ModelSniffer())->getAllRelations($this) as $relation => $attributes) {
            if (in_array($relation, self::$draftOptions->draftRelations)) {
                $relations[$relation] = $attributes;
            }
        }

        return $relations;
    }

    /**
     * @param Model $model
     * @param int $id
     * @param string $relation
     * @param array $attributes
     * @param Draft|null $draft
     */
    private function attachPivotedRelationFromExistingOrNewRelated(Model $model, $id, $relation, array $attributes = [], Draft $draft = null)
    {
        $related = $model->{$relation}()->getRelated()->findOrNew($id);

        if ($related->exists) {
            $model->{$relation}()->attach($id, $attributes);
        } elseif ($draft->exists) {
            if (isset($draft->metadata['relations'][$relation]['records']['items'])) {
                foreach ($draft->metadata['relations'][$relation]['records']['items'] as $item) {
                    if (isset($item['id']) && $item['id'] == $id) {
                        foreach ((array)$item as $field => $value) {
                            $related->{$field} = $value;
                        }

                        $related->save();
                        $model->{$relation}()->attach($id, $attributes);
                    }
                }
            }
        }
    }

    /**
     * Verify if the getDraftOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkDraftOptions()
    {
        if (!method_exists(self::class, 'getDraftOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getDraftOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getDraftOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getDraftOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}