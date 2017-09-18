<?php

namespace App\Options;

use App\Http\Filters\Filter;
use App\Http\Sorts\Sort;
use App\Models\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DraftOptions
{
    /**
     * The fields that should be draftable.
     * By default (null) all fields are draftable.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDrafts trait.
     *
     * @var array
     */
    public $draftFields = [];

    /**
     * The model's relations that should be draftable.
     * By default (null) none of the model's relations are draftable.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDrafts trait.
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
     * IMPORTANT: This option is available for the App\Traits\HasDrafts trait.
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
     * IMPORTANT: This option is available for the App\Traits\HasDrafts trait.
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
     * IMPORTANT: This option is available for the App\Traits\HasDrafts trait.
     *
     * @var bool
     */
    public $softDraftRelations = false;

    /**
     * The model that should be used for drafting.
     * When setting this, pass either an instance of App\Models\Model or a string.
     * The "setEntityModel()" method will convert it to a valid model.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var Model|string
     */
    public $entityModel;

    /**
     * The form request validator to validate against.
     * This is used inside the "limbo()" methods of the App\Traits\CanDraft trait.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var FormRequest
     */
    public $validatorRequest;

    /**
     * The filter class based on which the drafts could be filtered.
     * This is used inside the "drafts()" methods of the App\Traits\CanDraft trait.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var Filter
     */
    public $filterClass;

    /**
     * The sort class based on which the drafts could be sorted.
     * This is used inside the "drafts()" methods of the App\Traits\CanDraft trait.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var Sort
     */
    public $sortClass;

    /**
     * The meta title displayed when viewing an entity record's list of drafts.
     * This is used as the meta title for when viewing the entire list of drafts belonging to an entity record.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var string
     */
    public $listTitle = 'Drafts';

    /**
     * The meta title displayed when viewing an entity record's draft.
     * This is used as the meta title for when viewing a single draft belonging to an entity record.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var string
     */
    public $singleTitle = 'Draft';

    /**
     * The blade view file returned when viewing an entity record's list of drafts.
     * This is used to know which view to return when accessing the logic for displaying all of one entity's drafts.
     * More precisely, it's used inside the "drafts()" method of the App\Traits\CanDraft trait.
     *
     * When setting this, pass either an instance of Illuminate\View\View or a string.
     * The "setListView()" method will convert it to a valid view response.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var View|string
     */
    public $listView;

    /**
     * The blade view file returned when viewing an entity record's normal draft.
     * This is used to know which view to return when accessing the logic for displaying a specific draft belonging to an entity record.
     * More precisely, it's used inside the "draft()" method of the App\Traits\CanDraft trait.
     *
     * When setting this, pass either an instance of Illuminate\View\View or a string.
     * The "setSingleView()" method will convert it to a valid view response.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var View|string
     */
    public $singleView;

    /**
     * The blade view file returned when viewing an entity record's limbo draft.
     * This is used to know which view to return when accessing the logic for displaying a specific limbo draft.
     * More precisely, it's used inside the "drafts()" method of the App\Traits\CanDraft trait.
     *
     * When setting this, pass either an instance of Illuminate\View\View or a string.
     * The "setLimboView()" method will convert it to a valid view response.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var View|string
     */
    public $limboView;

    /**
     * The redirect url to redirect the admin user after a limbo draft has been saved.
     * This is used inside the "limbo()" method of the  App\Traits\CanDraft trait, more precisely in the PUT logic.
     *
     * When setting this, pass either an instance of Illuminate\Http\RedirectResponse or a string.
     * The "setRedirectUrl()" method will convert it to a valid redirect response to a route.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var RedirectResponse|string
     */
    public $redirectUrl;

    /**
     * The variables that will be assigned to the view when viewing an entity record's revision.
     * This is used on the "drafts()", "draft()" and "limbo()" methods of the App\Traits\CanDraft trait.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDraft trait.
     *
     * @var array
     */
    public $viewVariables = [];

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

    /**
     * Set the $validatorRequest to work with in the App\Traits\CanDraft trait.
     *
     * @param Model|string $model
     * @return DraftOptions
     */
    public function setEntityModel($model): DraftOptions
    {
        $this->entityModel = $model instanceof Model ? $model : app($model);

        return $this;
    }

    /**
     * Set the $validatorRequest to work with in the App\Traits\CanDraft trait.
     *
     * @param FormRequest $validator
     * @return DraftOptions
     */
    public function setValidatorRequest(FormRequest $validator): DraftOptions
    {
        $this->validatorRequest = $validator;

        return $this;
    }

    /**
     * Set the $filterClass to work with in the App\Traits\CanDraft trait.
     *
     * @param Filter $filter
     * @return DraftOptions
     */
    public function setFilterClass(Filter $filter): DraftOptions
    {
        $this->filterClass = $filter;

        return $this;
    }

    /**
     * Set the $sortClass to work with in the App\Traits\CanDraft trait.
     *
     * @param Sort $sort
     * @return DraftOptions
     */
    public function setSortClass(Sort $sort): DraftOptions
    {
        $this->sortClass = $sort;

        return $this;
    }

    /**
     * Set the $listTitle to work with in the App\Traits\CanDraft trait.
     *
     * @param string $title
     * @return DraftOptions
     */
    public function setListTitle($title): DraftOptions
    {
        $this->listTitle = $title;

        return $this;
    }

    /**
     * Set the $singleTitle to work with in the App\Traits\CanDraft trait.
     *
     * @param string $title
     * @return DraftOptions
     */
    public function setSingleTitle($title): DraftOptions
    {
        $this->singleTitle = $title;

        return $this;
    }

    /**
     * Set the $listView to work with in the App\Traits\CanDraft trait.
     *
     * @param View|string $view
     * @return DraftOptions
     */
    public function setListView($view): DraftOptions
    {
        $this->listView = $view instanceof View ? $view : view($view);

        return $this;
    }

    /**
     * Set the $singleView to work with in the App\Traits\CanDraft trait.
     *
     * @param View|string $view
     * @return DraftOptions
     */
    public function setSingleView($view): DraftOptions
    {
        $this->singleView = $view instanceof View ? $view : view($view);

        return $this;
    }

    /**
     * Set the $limboView to work with in the App\Traits\CanDraft trait.
     *
     * @param View|string $view
     * @return DraftOptions
     */
    public function setLimboView($view): DraftOptions
    {
        $this->limboView = $view instanceof View ? $view : view($view);

        return $this;
    }

    /**
     * Set the $redirectUrl to work with in the App\Traits\CanDraft trait.
     *
     * @param RedirectResponse|string $redirect
     * @return DraftOptions
     */
    public function setRedirectUrl($redirect): DraftOptions
    {
        $this->redirectUrl = $redirect instanceof RedirectResponse ? $redirect : redirect()->route($redirect);

        return $this;
    }

    /**
     * Set the $viewVariables to work with in the App\Traits\CanDraft trait.
     *
     * @param array $variables
     * @return DraftOptions
     */
    public function setViewVariables(array $variables = []): DraftOptions
    {
        $this->viewVariables = $variables;

        return $this;
    }
}