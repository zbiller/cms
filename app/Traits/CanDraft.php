<?php

namespace App\Traits;

use App\Exceptions\DraftException;
use App\Http\Filters\Filter;
use App\Http\Sorts\Sort;
use App\Models\Model;
use App\Models\Version\Draft;
use App\Options\DraftOptions;
use App\Services\CacheService;
use DB;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use Meta;
use ReflectionMethod;
use Route;

trait CanDraft
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\DraftOptions file.
     *
     * @var DraftOptions
     */
    protected static $draftOptions;

    /**
     * Instantiate the $draftOptions property with the necessary draft properties.
     *
     * @set $AuthenticateOptions
     */
    public static function bootCanDraft()
    {
        self::checkDraftOptions();

        self::$draftOptions = self::getDraftOptions();

        self::validateDraftOptions();
    }

    /**
     * Display the list of limbo drafts view.
     * Set the drafts list page meta title.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request)
    {
        $this->establishDraftsPageTitle();

        if (!$this->canBeDrafted()) {
            flash()->error('This entity does not support drafts!');
            return back();
        }

        $model = self::$draftOptions->entityModel;
        $filter = self::$draftOptions->filterClass;
        $sort = self::$draftOptions->sortClass;

        $query = $model->query()->onlyDrafts();

        if ($filter && $filter instanceof Filter) {
            $query->filtered($request, $filter);
        }

        if ($sort && $sort instanceof Sort) {
            $query->sorted($request, $sort);
        }

        return $this->listDraftViewWithVariables(
            $query->paginate(config('crud.per_page'))
        );
    }

    /**
     * Display the single draft view.
     * Set a back url in the session so we know where to redirect.
     * Set the draft page meta title.
     * Display the single draft view.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function draft(Request $request)
    {
        try {
            $draft = Draft::findOrFail(Route::current()->parameter('draft'));
        } catch (ModelNotFoundException $e) {
            flash()->error('The draft does not exist!');
            return back();
        }

        $this->rememberDraftBackUrl($draft);
        $this->establishDraftPageTitle();

        try {
            DB::beginTransaction();
            CacheService::disableQueryCache();

            $model = $draft->draftable;

            if (!($this->canBeDrafted() && $model && $model->exists)) {
                flash()->error('This entity does not support drafts!');
                return back();
            }

            $model->publishDraft($draft);

            return $this->singleDraftViewWithVariables($model, $draft);
        } catch (Exception $e) {
            DB::rollBack();

            flash()->error('Could not display the draft! Please try again.');
            return back();
        }
    }

    /**
     * Display the limbo draft view or save / publish the limbo draft, depending on the request method (GET | PUT).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function limbo(Request $request)
    {
        $id = Route::current()->parameter('id');

        if (!$this->canBeDrafted()) {
            flash()->error('This entity does not support drafts!');
            return back();
        }

        try {
            $model = self::$draftOptions->entityModel->onlyDrafts()->findOrFail($id);

            switch ($request->method()) {
                case 'GET':
                    $this->establishDraftPageTitle();

                    return $this->limboDraftViewWithVariables($model);
                    break;
                case 'PUT':
                    $validator = self::$draftOptions->validatorRequest;

                    if ($validator instanceof FormRequest) {
                        try {
                            $request->validate(
                                $this->parseDraftValidationRules(),
                                $validator->messages(),
                                $validator->attributes()
                            );
                        } catch (ValidationException $e) {
                            return back()->withErrors($e->validator->errors());
                        } catch (\Exception $e) {
                            dd($e);
                        }
                    }

                    try {
                        $model->saveAsDraft($request->all());

                        flash()->success('The draft was successfully saved!');
                    } catch (DraftException $e) {
                        flash()->error($e->getMessage());
                    } catch (Exception $e) {
                        flash()->error('Could not save or publish the draft! Please try again');
                    }

                    return self::$draftOptions->redirectUrl ?: back();
                    break;
            }
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }

    /**
     * Verify if the model can be drafted.
     * It has to use the App\Traits\HasDrafts trait.
     *
     * @return bool
     */
    protected function canBeDrafted()
    {
        return in_array(HasDrafts::class, class_uses(static::$draftOptions->entityModel));
    }

    /**
     * Remember the back url for when canceling, saving or publishing a draft.
     *
     * @param Draft $draft
     * @return void
     */
    protected function rememberDraftBackUrl(Draft $draft)
    {
        if (!session('draft_back_url_' . $draft->id)) {
            session()->put('draft_back_url_' . $draft->id, url()->previous());
        }
    }

    /**
     * Set the meta title for the drafts list view page.
     *
     * @return void
     */
    protected function establishDraftsPageTitle()
    {
        $title = self::$draftOptions->listTitle;

        Meta::set('title', $title ? 'Admin - ' . $title : 'Admin');
    }

    /**
     * Set the meta title for the draft single view page.
     *
     * @return void
     */
    protected function establishDraftPageTitle()
    {
        $title = self::$draftOptions->singleTitle;

        Meta::set('title', $title ? 'Admin - ' . $title : 'Admin');
    }

    /**
     * Build the single draft view with every required or specified variable.
     *
     * @param $items
     * @return \Illuminate\View\View
     */
    protected function listDraftViewWithVariables(LengthAwarePaginator $items)
    {
        return self::$draftOptions->listView->with(array_merge(
            self::$draftOptions->viewVariables,
            ['items' => $items]
        ));
    }

    /**
     * Build the single draft view with every required or specified variable.
     *
     * @param Model $model
     * @param Draft $draft
     * @return \Illuminate\View\View
     */
    protected function singleDraftViewWithVariables(Model $model, Draft $draft)
    {
        return self::$draftOptions->singleView->with(array_merge(
            self::$draftOptions->viewVariables,
            ['item' => $model, 'draft' => $draft]
        ));
    }

    /**
     * Build the limbo draft view with every required or specified variable.
     *
     * @param Model $model
     * @return \Illuminate\View\View
     */
    protected function limboDraftViewWithVariables(Model $model)
    {
        return self::$draftOptions->limboView->with(array_merge(
            self::$draftOptions->viewVariables,
            ['item' => $model]
        ));
    }

    /**
     * Parse the original form request validation rules into draftable rules.
     * Basically, strip any unique validation rule that might exist.
     *
     * @return mixed
     */
    protected function parseDraftValidationRules()
    {
        $validationRules = self::$draftOptions->validatorRequest->rules();

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
     * Check if mandatory revision options have been properly set from the controller.
     * Check if $model has been properly set.
     * Check if $redirect has been properly set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validateDraftOptions()
    {
        if (!self::$draftOptions->entityModel) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDraft trait.' . PHP_EOL .
                'You are required to set the "entityModel" that will throughout the drafting features.' . PHP_EOL .
                'You can do this from inside the getDraftOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the model must be an instance of App\Models\Model or a string.'
            );
        }

        if (!self::$draftOptions->listView) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDraft trait.' . PHP_EOL .
                'You are required to set the "listView" that will be returned when viewing all of an entity record\'s drafts.' . PHP_EOL .
                'You can do this from inside the getDraftOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the view must be an instance of Illuminate\View\View or a string.'
            );
        }

        if (!self::$draftOptions->singleView) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDraft trait.' . PHP_EOL .
                'You are required to set the "singleView" that will be returned when viewing a single entity draft.' . PHP_EOL .
                'You can do this from inside the getDraftOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the view must be an instance of Illuminate\View\View or a string.'
            );
        }

        if (!self::$draftOptions->limboView) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDraft trait.' . PHP_EOL .
                'You are required to set the "limboView" that will be returned when viewing a limbo draft.' . PHP_EOL .
                'You can do this from inside the getDraftOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the view must be an instance of Illuminate\View\View or a string.'
            );
        }

        if (!self::$draftOptions->redirectUrl) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDraft trait.' . PHP_EOL .
                'You are required to set the "redirectUrl" that will be redirected to after a limbo draft is saved.' . PHP_EOL .
                'You can do this from inside the getDraftOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the redirect must be an instance of Illuminate\Http\RedirectResponse or a string.'
            );
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
