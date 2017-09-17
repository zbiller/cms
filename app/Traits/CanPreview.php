<?php

namespace App\Traits;

use App\Options\PreviewOptions;
use App\Services\CacheService;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route as Router;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;

trait CanPreview
{
    /**
     * The model to be previewed.
     *
     * @var Model
     */
    protected $previewModel;

    /**
     * The form request validation class to validate the model before previewing it.
     *
     * @var FormRequest
     */
    protected $previewValidator;

    /**
     * The pivoted relations defined as an associative array where the:
     * - keys: represent each pivoted relation's name defined on the model.
     * - values: represent the request array key name responsible for passing data into the "attach" or "sync" methods.
     *
     * @var array
     */
    protected $previewPivotedRelations = [];

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\PreviewOptions file.
     *
     * @var PreviewOptions
     */
    protected static $previewOptions;

    /**
     * Instantiate the $AuthenticateOptions property with the necessary authentication properties.
     *
     * @set $AuthenticateOptions
     */
    public static function bootCanPreview()
    {
        self::checkPreviewOptions();

        self::$previewOptions = self::getPreviewOptions();

        self::validatePreviewOptions();
    }

    /**
     * Return the model set by the PreviewOptions class.
     *
     * @return Model
     */
    public function getPreviewModel()
    {
        if (!$this->previewModel) {
            return $this->previewModel = static::$previewOptions->model;
        }

        return $this->previewModel;
    }

    /**
     * Set the preview model.
     *
     * @param Model $model
     */
    public function setPreviewModel(Model $model)
    {
        $this->previewModel = $model;
    }

    /**
     * Return the validator set by the PreviewOptions class.
     *
     * @return FormRequest
     */
    public function getPreviewValidator()
    {
        if (!$this->previewValidator) {
            return $this->previewValidator = static::$previewOptions->validator;
        }

        return $this->previewValidator;
    }

    /**
     * Set the preview validator.
     *
     * @param FormRequest $validator
     */
    public function setPreviewValidator(FormRequest $validator)
    {
        $this->previewValidator = $validator;
    }

    /**
     * Return the pivoted relations set by the PreviewOptions class.
     *
     * @return FormRequest
     */
    public function getPreviewPivotedRelations()
    {
        if (!$this->previewPivotedRelations) {
            return $this->previewPivotedRelations = static::$previewOptions->pivotedRelations;
        }

        return $this->previewPivotedRelations;
    }

    /**
     * Set the preview pivoted relations.
     *
     * @param array $relations
     */
    public function setPreviewPivotedRelations(array $relations)
    {
        $this->previewPivotedRelations = $relations;
    }

    /**
     * Verify if a model can be previewed.
     * It has to have a url, more precisely, it has to use the App\Traits\HasUrl trait.
     *
     * @return bool
     */
    protected function canBePreviewed()
    {
        return in_array(HasUrl::class, class_uses($this->getPreviewModel()));
    }

    /**
     * Mark the current request as a preview request, so the underlying logic would know that.
     *
     * @return void
     */
    protected function markAsPreview()
    {
        session()->flash('is_preview', true);
    }

    /**
     * Preview an entity that has a url.
     *
     * @param Request $request
     * @param int|null $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function preview(Request $request, $id = null)
    {
        if (!$this->canBePreviewed()) {
            flash()->error('You cannot preview an entity that does not have a url!');
            return back();
        }

        $this->validatePreviewRequest($request);

        try {
            DB::beginTransaction();
            CacheService::disableQueryCache();

            $this->newOrExistingModelForPreview($id);
            $this->saveModelForPreview($request);

            foreach ($this->getPreviewPivotedRelations() as $relation => $field) {
                $this->savePivotedRelationForPreview($request, $relation, $field);
            }

            $this->markAsPreview();

            return $this->executePreviewRequest();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the model to a valid model record.
     * Based on the $id provided, the model will be new or loaded.
     *
     * @param int|null $id
     * @return void
     */
    protected function newOrExistingModelForPreview($id = null)
    {
        $model = $this->getPreviewModel();

        if ($id && is_numeric($id)) {
            try {
                $model = $model->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                abort(404);
            }
        }

        $this->setPreviewModel($model);
    }

    /**
     * Save the given model and it's defined pivoted relations with the request provided.
     * Persist the model saves to the model property to be used later.
     *
     * @param Request $request
     * @return void
     */
    protected function saveModelForPreview(Request $request)
    {
        $model = $this->getPreviewModel();

        if ($model && $model->exists) {
            $model->update($request->all());
        } else {
            $model = $model->create($request->all());
        }

        $this->setPreviewModel($model);
    }

    /**
     * Save a defined pivoted relation of the model for the preview.
     *
     * @param Request $request
     * @param string $relation
     * @param string $field
     */
    protected function savePivotedRelationForPreview(Request $request, $relation, $field)
    {
        $model = $this->getPreviewModel();
        $data = $request->input($field);

        $model->{$relation}()->detach();

        if (is_array($data)) {
            switch (array_depth($data)) {
                case 1:
                    $model->{$relation}()->attach($data);

                    break;
                case 2:
                    foreach ($data as $id => $attributes) {
                        $model->{$relation}()->attach($id, $attributes);
                    }

                    break;
                case 3:
                    foreach ($data as $index => $parameters) {
                        foreach ($parameters as $id => $attributes) {
                            $model->{$relation}()->attach($id, $attributes);
                        }
                    }

                    break;
            }
        }

        $this->setPreviewModel($model);
    }

    /**
     * Dispatch the request to the front-end endpoint defined inside the model class that's being previewed.
     * The routing is done based on the "controller" and "action" defined on the HasUrl trait.
     *
     * @return mixed
     */
    protected function executePreviewRequest()
    {
        $dispatcher = new ControllerDispatcher(app());
        $controller = $this->getPreviewModel()->getUrlOptions()->routeController;
        $action = $this->getPreviewModel()->getUrlOptions()->routeAction;

        return $dispatcher->dispatch(
            app(Router::class)->setAction([
                'model' => $this->getPreviewModel()
            ]), app($controller), $action
        );
    }

    /**
     * Validate the request based on the form request specified in the getPreviewOptions() method from controller.
     * If the rules don't pass, cancel the preview.
     *
     * @param Request $request
     * @return $this|void
     */
    protected function validatePreviewRequest(Request $request)
    {
        if (!($this->getPreviewValidator() instanceof FormRequest)) {
            return;
        }

        try {
            $request->validate(
                $this->parsePreviewValidationRules(),
                $this->getPreviewValidator()->messages(),
                $this->getPreviewValidator()->attributes()
            );
        } catch (ValidationException $e) {
            return back()->withErrors(
                $e->validator->errors()
            );
        }
    }

    /**
     * Parse the original form request validation rules into previewable rules.
     * Basically, strip any unique validation rule that might exist.
     *
     * @return mixed
     */
    protected function parsePreviewValidationRules()
    {
        $validationRules = $this->getPreviewValidator()->rules();

        foreach ($validationRules as $field => $rules) {
            if (is_array($rules)) {
                foreach ($rules as $index => $rule) {
                    if (@get_class($rule) == Unique::class || str_is('unique*', $rule)) {
                        unset($validationRules[$field][$index]);
                    }
                }
            } else {
                if (@get_class($rules) == Unique::class || str_is('unique*', $rules)) {
                    unset($validationRules[$field]);
                }
            }
        }

        return $validationRules;
    }

    /**
     * Check if mandatory preview options have been properly set from the controller.
     * Check if $model has been set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validatePreviewOptions()
    {
        if (!self::$previewOptions->model || !(self::$previewOptions->model instanceof Model)) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanPreview trait.' . PHP_EOL .
                'You are required to set the "model" that will be previewed.' . PHP_EOL .
                'You can do this from inside the getPreviewOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the validator must be an instance of App\Models\Model or a string.'
            );
        }
    }

    /**
     * Verify if the getPreviewOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkPreviewOptions()
    {
        if (!method_exists(self::class, 'getPreviewOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getPreviewOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getPreviewOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getPreviewOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}
