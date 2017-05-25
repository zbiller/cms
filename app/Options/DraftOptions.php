<?php

namespace App\Options;

class DraftOptions
{
    /**
     * The fields that should be draftable.
     * By default (null) all fields are draftable.
     *
     * @var array
     */
    public $draftFields = [];

    /**
     * The model's relations that should be draftable.
     * By default (null) none of the model's relations are draftable.
     *
     * @var array
     */
    public $draftRelations = [];

    /**
     * Flag indicating whether to delete the just published draft.
     *
     * If set to "true", when publishing a draft.
     * After the model instance has been updated with the draft's data.
     * The recently published draft will be deleted from the drafts database table.
     *
     * If set to "false", when publishing a draft.
     * After the model instance has been updated with the draft's data.
     * The recently published draft will be still be present in the drafts database table.
     *
     * @var bool
     */
    public $deletePublishedDraft = true;

    /**
     * Flag indicating whether to create a revision for the model, when publishing a draft to that model.
     *
     * If set to "true", after publishing a draft, the original model instance's data will be stored to a revision.
     *
     * If set to "false", after publishing a draft, the original model instance's data will NOT be stored to a revision.
     * Please note that if "false", there's the risk of losing valuable data.
     *
     * @var bool
     */
    public $createRevisionWhenPublishingDraft = true;

    /**
     * Flag indicating whether to force publish a draft's relations.
     *
     * If set to "false" every draftable relation will be updated.
     * This means that if a draftable relation data has not been provided to the SaveAsDraft method, when saving a model as a draft,
     * That relation will be saved empty and when publishing that draft, all of the empty draftable relations will be force updated,
     * Meaning that the model instance's relations will be saved as empty (no relation records).
     *
     * If set to "true" only provided draftable relations will be updated.
     * This means that if a draftable relation data has not been provided to the SaveAsDraft method, when saving a model as a draft,
     * That relation will be ignored when publishing that draft,
     * Meaning that the model instance will keep it's original relation data.
     *
     * This applies only for draftable relations which data has not been provided.
     * Please note that if EMPTY data is provided alongside to the SaveAsDraft method, those relations will get saved as empty, regardless this option.
     *
     * @var bool
     */
    public $softDraftRelations = false;

    /**
     * Get a fresh instance of this class.
     *
     * @return DraftOptions
     */
    public static function instance(): DraftOptions
    {
        return new static();
    }

    /**
     * Set the $draftFields to work with in the App\Traits\HasDrafts trait.
     *
     * @param ...$fields
     * @return DraftOptions
     */
    public function fieldsToDraft(...$fields): DraftOptions
    {
        $this->draftFields = array_flatten($fields);

        return $this;
    }

    /**
     * Set the $draftRelations to work with in the App\Traits\HasDrafts trait.
     *
     * @param ...$relations
     * @return DraftOptions
     */
    public function relationsToDraft(...$relations): DraftOptions
    {
        $this->draftRelations= array_flatten($relations);

        return $this;
    }

    /**
     * Set the $deletePublishedDraft to work with in the App\Traits\HasDrafts trait.
     *
     * @return DraftOptions
     */
    public function doNotDeletePublishedDrafts(): DraftOptions
    {
        $this->deletePublishedDraft = false;

        return $this;
    }

    /**
     * Set the $createRevisionWhenPublishingDraft to work with in the App\Traits\HasDrafts trait.
     *
     * @return DraftOptions
     */
    public function disableRevisioningWhenPublishingADraft(): DraftOptions
    {
        $this->createRevisionWhenPublishingDraft = false;

        return $this;
    }

    /**
     * Set the $softDraftRelations to work with in the App\Traits\HasDrafts trait.
     *
     * @return DraftOptions
     */
    public function softRelationDrafting(): DraftOptions
    {
        $this->softDraftRelations = true;

        return $this;
    }
}