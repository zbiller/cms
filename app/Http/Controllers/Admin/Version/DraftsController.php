<?php

namespace App\Http\Controllers\Admin\Version;

use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Validator;
use Exception;
use Throwable;
use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Models\Version\Draft;
use App\Exceptions\DraftException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class DraftsController extends Controller
{
    /**
     * The loaded model record.
     *
     * @var Model
     */
    protected $model;

    /**
     * The model class.
     *
     * @var string
     */
    protected $class;

    /**
     * The entity request.
     *
     * @var Request
     */
    protected $request;

    /**
     * The drafts belonging to the draftable model instance.
     *
     * @var Collection
     */
    protected $drafts;

    /**
     * The route to redirect back to.
     *
     * @var string
     */
    protected $route;

    /**
     * The model id if it exists.
     *
     * @var int
     */
    protected $id;

    /**
     * Get the drafts.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getDrafts(Request $request)
    {
        $this->validateDraftableAjaxData($request);

        try {
            $this->drafts = $this->getDraftRecords($request);
            $this->route = $request->get('route');

            return [
                'status' => true,
                'html' => $this->buildTableHtml(),
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
            ];
        }
    }

    /**
     * Save a draft.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws DraftException
     * @throws Exception
     */
    public function saveDraft(Request $request)
    {
        try {
            $this->validateDraftCreationData($request->all());

            $this->class = $request->get('_class');
            $this->request = $request->get('_request');
            $this->id = $request->get('_id');

            $this->validateOriginalEntityData($request->all());
        } catch (ValidationException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all())->withErrors($e->validator->errors());
        } catch (InvalidArgumentException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        try {
            $this->model = app($this->class)->findOrFail($this->id);
        } catch (Exception $e) {
            $this->model = app($this->class);
        }

        try {
            $this->model->saveAsDraft($request->all());

            flash()->success('The draft was successfully saved!');
            return back();
        } catch (DraftException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all());
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create a draft.
     *
     * @param Draft $draft
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function createDraft(Draft $draft, Request $request)
    {
        try {
            $this->validateDraftCreationData($request->all());

            $this->class = $request->get('_class');
            $this->request = $request->get('_request');
            $this->id = $request->get('_id');

            $this->validateOriginalEntityData($request->all());

            $model = $draft->draftable;
            $data = $request->except(['_token', '_method']);

            $model->saveAsDraft($data);

            flash()->success('The draft was successfully created!');
            return back();
        } catch (DraftException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all());
        } catch (ValidationException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all())->withErrors($e->validator->errors());
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update a draft.
     *
     * @param Draft $draft
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function updateDraft(Draft $draft, Request $request)
    {
        try {
            $this->validateDraftCreationData($request->all());

            $this->class = $request->get('_class');
            $this->request = $request->get('_request');
            $this->id = $request->get('_id');

            $this->validateOriginalEntityData($request->all());

            $model = $draft->draftable;
            $data = $request->except(['_token', '_method']);

            if (!empty($data)) {
                $model->saveAsDraft($data, $draft);
            }

            flash()->success('The draft was successfully updated!');
            return back();
        } catch (DraftException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all());
        } catch (ValidationException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all())->withErrors($e->validator->errors());
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Publish a draft.
     *
     * @param Draft $draft
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function publishDraft(Draft $draft, Request $request)
    {
        try {
            if ($request->has('_class')) {
                $this->class = $request->get('_class');
            }

            if ($request->has('_request')) {
                $this->request = $request->get('_request');
            }

            if ($request->has('_id')) {
                $this->id = $request->get('_id');
            }

            $this->validateOriginalEntityData($request->all());

            $model = $draft->draftable;
            $data = $request->except(['_token', '_method']);
            $redirect = session()->pull('draft_back_url_' . $draft->id);

            DB::transaction(function () use ($model, $draft, $data) {
                if (!empty($data)) {
                    $model->saveAsDraft($data, $draft);
                }

                $model->publishDraft($draft->fresh());
            });

            flash()->success('The draft was successfully published!');

            if (request()->ajax()) {
                return ['status' => true];
            }

            return $redirect ? redirect($redirect) : back();
        } catch (DraftException $e) {
            flash()->error($e->getMessage());

            if (request()->ajax()) {
                return ['status' => true];
            }

            return back()->withInput($request->all());
        } catch (ValidationException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all())->withErrors($e->validator->errors());
        } catch (Exception $e) {
            if (request()->ajax()) {
                return ['status' => true];
            }

            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Publish a limbo draft.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function publishLimboDraft(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '_class' => 'required',
            '_id' => 'required',
        ]);

        if ($validator->fails()) {
            flash()->error('Could not publish the draft! Please try again.');
            return back();
        }

        try {
            if ($request->has('_request')) {
                $this->request = $request->get('_request');
            }

            $this->validateOriginalEntityData($request->all());

            $class = $request->get('_class');
            $id = $request->get('_id');
            $data = $request->except(['_token', '_method', '_back', '_class', '_request', '_id']);
            $model = $class::onlyDrafts()->findOrFail($id);

            DB::transaction(function () use ($model, $data) {
                if (!empty($data)) {
                    $model->saveAsDraft($data);
                }

                $model->publishDraft();
            });

            flash()->success('The draft was successfully published!');
            return $request->get('_back') ? redirect($request->get('_back')) : back();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to publish a draft that does not exist!');
            return $request->get('_back') ? redirect($request->get('_back')) : back();
        } catch (ValidationException $e) {
            flash()->error($e->getMessage());
            return back()->withInput($request->all())->withErrors($e->validator->errors());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Remove a draft.
     *
     * @param Request $request
     * @param Draft $draft
     * @return array|mixed
     */
    public function removeDraft(Request $request, Draft $draft)
    {
        $this->validateDraftableAjaxData($request);

        try {
            return DB::transaction(function () use ($request, $draft) {
                $draft->delete();

                $this->drafts = $this->getDraftRecords($request);
                $this->route = $request->get('route');

                return [
                    'status' => true,
                    'html' => $this->buildTableHtml(),
                ];
            });
        } catch (Exception $e) {
            return [
                'status' => false,
            ];
        }
    }

    /**
     * Delete a limbo draft.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function deleteLimboDraft(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '_class' => 'required',
            '_id' => 'required',
        ]);

        if ($validator->fails()) {
            flash()->error('Could not delete the draft! Please try again.');
            return back();
        }

        try {
            $class = $request->get('_class');
            $id = $request->get('_id');
            $model = $class::onlyDrafts()->findOrFail($id);

            $model->deleteDraft();

            flash()->success('The draft was successfully deleted!');
            return back();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to delete a draft that does not exist!');
            return back();
        } catch (DraftException $e) {
            flash()->error($e->getMessage());
            return back();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get the drafts belonging to a draftable model.
     *
     * @param Request $request
     * @return mixed
     */
    protected function getDraftRecords(Request $request)
    {
        return Draft::with('user')->whereDraftable(
            $request->get('draftable_id'),
            $request->get('draftable_type')
        )->newest()->get();
    }

    /**
     * Populate the draft helper table with the updated drafts and route.
     * Return the html as string.
     *
     * @return mixed
     * @throws Exception
     * @throws Throwable
     */
    protected function buildTableHtml()
    {
        return view('helpers::draft.partials.table')->with([
            'drafts' => $this->drafts,
            'route' => $this->route,
        ])->render();
    }

    /**
     * Validate the draftable data coming from an ajax request from the draft helper.
     *
     * @param Request $request
     * @return void
     */
    protected function validateDraftableAjaxData(Request $request)
    {
        $this->validate($request, [
            'draftable_id' => 'required|numeric',
            'draftable_type' => 'required',
            'route' => 'required',
        ]);
    }

    /**
     * Validate the crucial request data needed for creating a draft.
     * _class | _request | _id (optional)
     *
     * @param array $data
     * @return void
     * @throws Exception
     */
    protected function validateDraftCreationData(array $data = [])
    {
        $validator = Validator::make($data, [
            '_class' => 'required',
            '_request' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                'To be able to save a draft, please add the following hidden fields to the entity form' . PHP_EOL .
                '"_class" => The fully qualified class name of the entity model' . PHP_EOL .
                '"_request" => The fully qualified class name of the request validating the entity model' . PHP_EOL .
                '"_id" (optional) => The id of the entity model (if model exists = on edit)'
            );
        }
    }

    /**
     * Validate the entity's request data based on the request rules provided.
     *
     * @param array $data
     * @throws ValidationException
     */
    protected function validateOriginalEntityData(array $data = [])
    {
        if (!$this->request) {
            return;
        }

        $validation = (new $this->request)->rules();

        foreach ($validation as $field => $rules) {
            if (is_array($rules)) {
                foreach ($rules as $index => $rule) {
                    if (@get_class($rule) == 'Illuminate\Validation\Rules\Unique' || str_is('unique*', $rule)) {
                        unset($validation[$field][$index]);
                    }
                }
            } else {
                if (@get_class($rules) == 'Illuminate\Validation\Rules\Unique' || str_is('unique*', $rules)) {
                    unset($validation[$field]);
                }
            }
        }

        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Initialize draft data.
     * Validate draft data based on original entity form request.
     *
     * @param Request $request
     * @return $this
     * @throws Exception
     */
    protected function doInitAndValidate(Request $request)
    {
        if ($request->has('_class')) {
            $this->class = $request->get('_class');
        }

        if ($request->has('_request')) {
            $this->request = $request->get('_request');
        }

        if ($request->has('_id')) {
            $this->id = $request->get('_id');
        }

        $this->validateOriginalEntityData($request->all());
    }
}