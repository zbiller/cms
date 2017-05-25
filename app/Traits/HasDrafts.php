<?php

namespace App\Traits;

use App\Models\Cms\Block;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Relation;
use Closure;
use Exception;
use ReflectionMethod;
use BadMethodCallException;
use App\Models\Model;
use App\Models\Version\Draft;
use App\Scopes\DraftingScope;
use App\Options\DraftOptions;
use App\Sniffers\ModelSniffer;
use App\Exceptions\DraftException;

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
                if ($this->wasRecentlyCreated || !$this->exists || !is_null($this->{$this->getDraftedAtColumn()})) {
                    $model = $this->saveLimboDraft($data);
                } else {
                    $model = $this->saveRegularDraft($data, $draft);
                }

                $this->fireModelEvent('drafted', false);

                return $model;
            });

            return $model;
        } catch (Exception $e) {
            throw new DraftException(
                'Could not save the draft for the record!', $e->getCode(), $e
            );
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
            throw new DraftException(
                'Could not publish the record!', $e->getCode(), $e
            );
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
            throw new DraftException(
                'Could not delete the draft!', $e->getCode, $e
            );
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
            throw new DraftException(
                'Could not delete the record\'s drafts!', $e->getCode(), $e
            );
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

        if (isset($draft->metadata->relations)) {
            foreach ($draft->metadata->relations as $relation => $attributes) {
                if (Relation::isDirect($attributes->type)) {
                    $this->publishDirectRelationFromDraft($relation, $attributes);
                }

                if (Relation::isPivoted($attributes->type)) {
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
            if (
                (!isset($params[$relation]) || empty($params[$relation])) &&
                self::$draftOptions->softDraftRelations === true
            ) {
                continue;
            }

            if (Relation::isDirect($attributes['type'])) {
                $data['relations'][$relation] = $this->buildDraftDataFromDirectRelation($params, $relation, $attributes);
            }

            if (Relation::isPivoted($attributes['type'])) {
                $data['relations'][$relation] = $this->buildDraftDataFromPivotedRelation($params, $relation, $attributes, $draft);
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
        if (!function_exists('attach_from_existing_or_new_related')) {
            /**
             * @param Model $model
             * @param int $id
             * @param string $relation
             * @param array $attributes
             * @param Draft|null $draft
             */
            function attach_from_existing_or_new_related(Model $model, $id, $relation, array $attributes = [], Draft $draft = null) {
                $related = $model->{$relation}()->getRelated()->findOrNew($id);

                if ($related->exists) {
                    $model->{$relation}()->attach($id, $attributes);
                } elseif ($draft->exists) {
                    if (isset($draft->metadata->relations->{$relation}->records->items)) {
                        foreach ($draft->metadata->relations->{$relation}->records->items as $item) {
                            if (isset($item->id) && $item->id == $id) {
                                foreach ((array)$item as $field => $value) {
                                    $related->{$field} = $value;
                                }

                                $related->save();
                                $model->{$relation}()->attach($id, $attributes);
                            }
                        }
                    }
                }
            };
        }

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
                    foreach ($parameters as $id => $attributes) {
                        attach_from_existing_or_new_related($this, $id, $relation, $attributes, $draft);
                    }
                } else {
                    attach_from_existing_or_new_related($this, $parameters, $relation, $attributes, $draft);
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
                $this->{$field} = $value;
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
        if (Relation::isParent($attributes->type)) {
            $this->publishDirectParentRelationFromDraft($relation, $attributes);
        } elseif (Relation::isChild($attributes->type)) {
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
        foreach ($attributes->records->items as $item) {
            $rel = $this->{$relation}()->findOrNew(
                isset($item->{$attributes->records->primary_key}) ?
                    $item->{$attributes->records->primary_key} : null
            );

            foreach ($item as $field => $value) {
                if ($field != $attributes->records->primary_key) {
                    $rel->attributes[$field] = $value;
                }
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
        $this->{$relation}()->delete();

        foreach ($attributes->records->items as $item) {
            $rel = $this->{$relation}()->getRelated()->newInstance();

            foreach ($item as $field => $value) {
                if ($field != $attributes->records->primary_key) {
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
        foreach ($attributes->records->items as $item) {
            $rel = $this->{$relation}()->getRelated()->findOrNew(
                isset($item->{$attributes->records->primary_key}) ?
                    $item->{$attributes->records->primary_key} : null
            );

            if ($rel->exists === false) {
                foreach ((array)$item as $field => $value) {
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