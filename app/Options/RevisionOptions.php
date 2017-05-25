<?php

namespace App\Options;

class RevisionOptions
{
    /**
     * Flag whether to make a revision on model creation.
     *
     * @var bool
     */
    public $revisionOnCreate = false;

    /**
     * The limit of revisions to be created for a model instance.
     * If the limit is reached, oldest revisions will start getting deleted to make room for new ones.
     *
     * @var int
     */
    public $revisionLimit;

    /**
     * The fields that should be revisionable.
     * By default (null) all fields are revisionable.
     *
     * @var array
     */
    public $revisionFields = [];

    /**
     * The model's relations that should be revisionable.
     * By default (null) none of the model's relations are revisionable.
     *
     * @var array
     */
    public $revisionRelations = [];

    /**
     * Flag indicating whether to create a revision for the model, when rolling back another revision of that model.
     * If set to "true", before rolling back a revision, the original model instance's data will be stored to a new revision.
     * If set to "false", after rolling back a revision, the original model instance's data will NOT be stored to a new revision.
     *
     * @var bool
     */
    public $createRevisionWhenRollingBack = true;

    /**
     * Get a fresh instance of this class.
     *
     * @return RevisionOptions
     */
    public static function instance(): RevisionOptions
    {
        return new static();
    }

    /**
     * Set the $revisionOnCreate to work with in the App\Traits\HasRevisions trait.
     *
     * @return RevisionOptions
     */
    public function enableRevisionOnCreate(): RevisionOptions
    {
        $this->revisionOnCreate = true;

        return $this;
    }

    /**
     * Set the $revisionLimit to work with in the App\Traits\HasRevisions trait.
     *
     * @param int $limit
     * @return RevisionOptions
     */
    public function limitRevisionsTo($limit): RevisionOptions
    {
        $this->revisionLimit = (int)$limit > 0 ? (int)$limit : null;

        return $this;
    }

    /**
     * Set the $revisionFields to work with in the App\Traits\HasRevisions trait.
     *
     * @param ...$fields
     * @return RevisionOptions
     */
    public function fieldsToRevision(...$fields): RevisionOptions
    {
        $this->revisionFields = array_flatten($fields);

        return $this;
    }

    /**
     * Set the $revisionRelations to work with in the App\Traits\HasRevisions trait.
     *
     * @param ...$relations
     * @return RevisionOptions
     */
    public function relationsToRevision(...$relations): RevisionOptions
    {
        $this->revisionRelations = array_flatten($relations);

        return $this;
    }

    /**
     * Set the $createRevisionWhenRollingBack to work with in the App\Traits\HasRevisions trait.
     *
     * @return RevisionOptions
     */
    public function disableRevisioningWhenRollingBack(): RevisionOptions
    {
        $this->createRevisionWhenRollingBack = false;

        return $this;
    }
}