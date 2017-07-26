<?php

namespace App\Traits;

use DB;
use Route;
use Closure;
use Exception;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Services\CacheService;
use App\Exceptions\CrudException;
use App\Exceptions\DuplicateException;
use App\Exceptions\UrlException;
use App\Exceptions\DraftException;
use App\Exceptions\RevisionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Illuminate\Routing\Route as Router;

trait CanCrud
{
    /**
     * The collection of existing records from the database.
     * Setting the $items should be done at controller level, in the callback.
     *
     * @var Collection
     */
    protected $items;

    /**
     * The loaded model.
     * Loading should be done at controller level, in the callback.
     *
     * @var Model
     */
    protected $item;

    /**
     * The title of the page.
     * This is used to build the meta title tag.
     *
     * @var string
     */
    protected $title;

    /**
     * The view to be returned for a given request.
     * Setting the $view should be done at controller level, in the callback.
     *
     * @var View
     */
    protected $view;

    /**
     * The redirect to be returned for a given request.
     * Setting the $redirect should be done at controller level, in the callback.
     *
     * @var RedirectResponse
     */
    protected $redirect;

    /**
     * All the variables that will be assigned to the view.
     * You can also use this property to assign variables to the view at controller level, in the callback.
     *
     * @var array
     */
    protected $vars = [];

    /**
     * Mapping of action => method.
     * Used to verify if a CRUD system respects the standards.
     *
     * @var array
     */
    protected static $crudMethods = [
        'index' => [
            'GET',
        ],
        'create' => [
            'GET',
        ],
        'store' => [
            'POST',
        ],
        'edit' => [
            'GET',
        ],
        'update' => [
            'PUT',
        ],
        'destroy' => [
            'DELETE',
        ],
        'deleted' => [
            'GET',
        ],
        'restore' => [
            'PUT',
        ],
        'delete' => [
            'DELETE',
        ],
        'duplicate' => [
            'POST',
        ],
        'drafts' => [
            'GET',
        ],
        'draft' => [
            'GET',
        ],
        'limbo' => [
            'GET',
            'PUT',
        ],
        'revision' => [
            'GET',
        ],
    ];

    /**
     * The list of exceptions that are soft (not throwable).
     * When the script fails with one of these exceptions, instead of throwing it, it will output an error message to the user.
     * 
     * @var array
     */
    protected static $softExceptions = [
        CrudException::class,
        UploadException::class,
        DraftException::class,
        RevisionException::class,
        UrlException::class,
        DuplicateException::class,
    ];

    /**
     * This method should be called inside the controller's index() method.
     * The closure should at least set the $items and $view properties.
     *
     * $this->items = Model::get();
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _index(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->checkCrudItems();
            $this->initCrudItems();

            $this->vars['items'] = $this->items;
        });
    }

    /**
     * This method should be called inside the controller's create() method.
     * The closure should at least set the $view property.
     *
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _create(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->model;
        });
    }

    /**
     * This method should be called inside the controller's store() method.
     * The closure should at least create the database record and set the $redirect property.
     *
     * $this->item = Model::create($request()->all());
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _store(Closure $function = null, Request $request = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function, $request) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success(__('crud.create_success'));
        }, $request);
    }

    /**
     * This method should be called inside the controller's edit() method.
     * The closure should at least attempt to find the record in the database.
     *
     * $this->item = Model::findOrFail($id); OR $this->item = $model; (if implicit route model binding)
     * $this->view = view('view.file');
     *
     * Although not required but strongly recommended is to also set a redirect in case somethind fails.
     *
     * $this->redirect = redirect()->route('redirect.route');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return RedirectResponse
     * @throws Exception
     */
    public function _edit(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->item ?: $this->model;
        });
    }

    /**
     * This method should be called inside the controller's update() method.
     * The closure should at least attempt to find and update the record in the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->update($request->all());
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function _update(Closure $function = null, Request $request = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function, $request) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success(__('crud.update_success'));
        }, $request);
    }

    /**
     * This method should be called inside the controller's destroy() method.
     * The closure should at least attempt to find and delete the record from the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->delete();
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @return RedirectResponse
     * @throws Exception
     */
    public function _destroy(Closure $function = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success(__('crud.delete_success'));
        });
    }

    /**
     * This method should be called inside the controller's deleted() method.
     * The closure should at least set the $items and $view properties.
     *
     * $this->items = Model::get();
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _deleted(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->checkCrudItems();
            $this->initCrudItems();

            $this->vars['items'] = $this->items;
        });
    }

    /**
     * This method should be called inside the controller's restore() method.
     * The closure should at least attempt to find and delete the record from the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->restore();
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @return RedirectResponse
     * @throws Exception
     */
    public function _restore(Closure $function = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success(__('crud.restore_success'));
        });
    }

    /**
     * This method should be called inside the controller's delete() method.
     * The closure should at least attempt to find and delete the record from the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->forceDelete();
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @return RedirectResponse
     * @throws Exception
     */
    public function _delete(Closure $function = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success(__('crud.delete_success'));
        });
    }

    /**
     * This method should be called inside the controller's duplicate() method.
     * The closure should at least attempt to duplicate the record from the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->forceDelete();
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function _duplicate(Closure $function = null, Request $request = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function, $request) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success(__('crud.duplicate_success'));
        }, $request);
    }

    /**
     * This method should be called inside the controller's preview() method.
     * The closure should only attempt to create/update the record from the database.
     *
     * $this->item = $model;
     * $this->item->update($request->all());
     *
     * OR
     *
     * $this->item = Model::create($request->all());
     *
     * @param Closure|null $function
     * @return RedirectResponse|mixed
     * @throws Exception
     */
    public function _preview(Closure $function = null)
    {
        $this->checkCrudMethod();
        $this->checkCrudModel();
        $this->initCrudModel();

        if (!in_array(HasUrl::class, class_uses($this->model))) {
            flash()->error('You cannot preview an entity that does not have an URL!');
            return back();
        }

        try {
            DB::beginTransaction();
            CacheService::disableQueryCache();

            if ($function) {
                call_user_func($function);
            }

            session()->flash('is_preview', true);

            return (new ControllerDispatcher(app()))->dispatch(app(Router::class)->setAction([
                'model' => $this->item
            ]), app($this->item->getUrlOptions()->routeController), $this->item->getUrlOptions()->routeAction);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * This method should be called inside the controller's index() method.
     * The closure should at least set the $items and $view properties.
     *
     * $this->items = Model::get();
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _drafts(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->checkCrudItems();
            $this->initCrudItems();

            $this->vars['items'] = $this->items;
        });
    }

    /**
     * This method should be called inside the controller's draft() method.
     * The closure should at least set the $item and $view properties and publish the draft.
     *
     * $this->item = $draft->draftable;
     * $this->item->publishDraft($draft);
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure $function
     * @param Draft $draft
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function _draft(Closure $function, Draft $draft)
    {
        return $this->performGetCrudRequest(function () use ($function, $draft) {
            DB::beginTransaction();
            CacheService::disableQueryCache();

            if (!session('draft_back_url_' . $draft->id)) {
                session()->put('draft_back_url_' . $draft->id, url()->previous());
            }

            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->item;
            $this->vars['draft'] = $draft;
        });
    }

    /**
     * This method should be called inside the controller's revision() method.
     * The closure should at least set the $item and $view properties and publish the draft.
     *
     * $this->item = $revision->revisionable;
     * $this->item->rollbackToRevision($revision);
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure $function
     * @param Revision $revision
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function _revision(Closure $function, Revision $revision)
    {
        return $this->performGetCrudRequest(function () use ($function, $revision) {
            DB::beginTransaction();

            if (!session('revision_back_url_' . $revision->id)) {
                session()->put('revision_back_url_' . $revision->id, url()->previous());
            }

            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->item;
            $this->vars['revision'] = $revision;
        });
    }

    /**
     * This method should be called inside the controller's limbo() method.
     * There are 2 closures to be used here, because the same controller action is used for both "GET" and "PUT".
     *
     * For the "GET" request the closure should at least set the $view property.
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * For the "PUT" request, the closure should at least save the draft and set the $redirect property.
     *
     * $this->item->saveAsDraft($request->all());
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure $getFunction
     * @param Closure $putFunction
     * @param int $id
     * @param Request $request
     * @param FormRequest $req
     * @return RedirectResponse
     * @throws Exception
     */
    public function _limbo(Closure $getFunction, Closure $putFunction, $id, Request $request, FormRequest $req)
    {
        try {
            $this->item = app($this->model)->onlyDrafts()->findOrFail($id);

            switch ($request->method()) {
                case 'GET':
                    if ($getFunction) {
                        call_user_func($getFunction);
                    }

                    $this->vars['item'] = $this->item;

                    if ($this->title) {
                        $this->setMeta('title', $this->title ? 'Admin - ' . $this->title : 'Admin');
                        $this->vars['title'] = $this->title;
                    }

                    return $this->view->with($this->vars);
                    break;
                case 'PUT':
                    try {
                        $validation = $req->rules();

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

                        $this->validate($request, $validation, $req->messages(), $req->attributes());
                    } catch (ValidationException $e) {
                        return back()->withErrors($e->validator->errors());
                    }

                    try {
                        if ($putFunction) {
                            call_user_func($putFunction);
                        }

                        flash()->success(__('crud.draft_save_success'));
                    } catch (DraftException $e) {
                        flash()->error($e->getMessage());
                    } catch (Exception $e) {
                        flash()->error($e->getMessage());

                        if (!in_array(get_class($e), self::$softExceptions)) {
                            throw $e;
                        }
                    }

                    return $this->redirect ?: back();
                    break;
            }
        } catch (ModelNotFoundException $e) {
            flash()->error(__('crud.draft_not_found'));
            return $this->redirect ?: back();
        }
    }

    /**
     * Perform a get crud request based on a closure.
     * The general logic resides on this method.
     * This means that the $function parameter should be a closure representing the special logic.
     *
     * @param Closure $function
     * @return RedirectResponse|View
     * @throws Exception
     */
    protected function performGetCrudRequest(Closure $function)
    {
        $this->checkCrudMethod();
        $this->checkCrudModel();
        $this->initCrudModel();

        if ($query = parse_url(url()->current(), PHP_URL_QUERY)) {
            session()->put('crud_query', $query);
        } else {
            session()->forget('crud_query');
        }

        try {
            call_user_func($function);

            $this->checkCrudView();
            $this->initCrudView();

            if ($this->title) {
                $this->setMeta('title', $this->title ? 'Admin - ' . $this->title : 'Admin');
                $this->vars['title'] = $this->title;
            }

            return $this->view->with($this->vars);
        } catch (ModelNotFoundException $e) {
            flash()->error(__('crud.model_not_found'));
        } catch (Exception $e) {
            if (in_array(get_class($e), self::$softExceptions)) {
                flash()->error($e->getMessage());
            } else {
                throw $e;
            }
        }

        if ($this->redirect) {
            $query = session()->pull('crud_query');

            return redirect($this->redirect->getTargetUrl() . ($query ? '?' . $query : ''));
        }

        return back();
    }

    /**
     * Perform a non-get crud request based on a closure.
     * The general logic resides on this method.
     * This means that the $function parameter should be a closure representing the special logic.
     *
     * @param Closure $function
     * @param Request|null $request
     * @return RedirectResponse
     * @throws Exception
     */
    protected function performNonGetCrudRequest(Closure $function, Request $request = null)
    {
        $this->checkCrudMethod();
        $this->checkCrudModel();
        $this->initCrudModel();

        try {
            DB::beginTransaction();

            call_user_func($function);

            $this->checkCrudRedirect();
            $this->initCrudRedirect();

            if (!session()->has('crud_query')) {
                session()->put('crud_query', parse_url(url()->previous(), PHP_URL_QUERY));
            }

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            flash()->error(__('crud.model_not_found'));
        } catch (Exception $e) {
            DB::rollBack();

            if (in_array(get_class($e), self::$softExceptions)) {
                flash()->error($e->getMessage());
                return back()->withInput($request ? $request->all() : []);
            }

            throw $e;
        }

        if ($request && $request->has('save_stay')) {
            return back();
        }

        if ($this->redirect) {
            $query = session()->pull('crud_query');

            return redirect($this->redirect->getTargetUrl() . ($query ? '?' . $query : ''));
        }

        return back();
    }

    /**
     * Verify if the current action runs under the appropriate CRUD method.
     *
     * @throws Exception
     * @return void
     */
    protected function checkCrudMethod()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (isset(self::$crudMethods[$action])) {
            if (!in_array(request()->method(), self::$crudMethods[$action])) {
                throw new Exception(
                    'Action ' . $action . '() of class ' . get_class($this) . ' must use the ' . self::$crudMethods[$action] . ' request method!' . PHP_EOL .
                    'Please set this in your route definition for this request.'
                );
            }
        }
    }

    /**
     * Verify if the protected $model property has been properly set on the controller.
     *
     * @throws Exception
     * @return void
     */
    protected function checkCrudModel()
    {
        if (!isset($this->model) || !$this->model || !is_string($this->model) || !class_exists($this->model)) {
            throw new Exception(
                'The $model property is not defined or is incorrect.' . PHP_EOL .
                'Please define a protected property $model on the ' . get_class($this) . ' class.' . PHP_EOL .
                'The $model should contain the entity\'s model full class name you wish to crud, as string.' . PHP_EOL .
                'Example: protected $model = "Full\Namespace\Model\Class";'
            );
        }
    }

    /**
     * Verify if the $view property has been properly assigned inside the callback of the given method.
     *
     * @throws Exception
     * @return void
     */
    protected function checkCrudView()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (!$this->view || (!($this->view instanceof View) && !is_string($this->view))) {
            throw new Exception(
                'The $view property is not defined or is incorrect.' . PHP_EOL .
                'Please instantiate the $view property on the ' . $controller . ' inside the callback of the ' . $action . '() method.' . PHP_EOL .
                'The $view should be either a string, or an instance of the Illuminate\View\View class.' . PHP_EOL .
                'Example: $this->view = view("view.file");'
            );
        }
    }

    /**
     * Verify if the $redirect property has been properly assigned inside the callback of the given method.
     *
     * @throws Exception
     * @return void
     */
    protected function checkCrudRedirect()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (!$this->redirect || (!($this->redirect instanceof RedirectResponse) && !is_string($this->redirect))) {
            throw new Exception(
                'The $redirect property is not defined or is incorrect.' . PHP_EOL .
                'Please instantiate the $redirect property on the ' . $controller . ' inside the callback of the ' . $action . '() method.' . PHP_EOL .
                'The $redirect should be either a string representing a URL, or an instance of the Illuminate\Http\RedirectResponse class.' . PHP_EOL .
                'Example: $this->redirect = redirect()->route("redirect.route");'
            );
        }
    }

    /**
     * Verify if the $redirect property has been properly assigned inside the callback of the given method.
     *
     * @throws Exception
     * @return void
     */
    protected function checkCrudItems()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (!$this->items || (!($this->items instanceof Collection) && !($this->items instanceof LengthAwarePaginator) && !is_array($this->items))) {
            throw new Exception(
                'The $items property is not defined or is incorrect.' . PHP_EOL .
                'Please instantiate the $items property on the ' . $controller . ' inside the callback of the ' . $action . '() method.' . PHP_EOL .
                'The $items should be either an array or an instance of Illuminate\Database\Eloquent\Collection or Illuminate\Contracts\Pagination\LengthAwarePaginator.' . PHP_EOL .
                'Example: $this->items = Model::queryScope()->paginate();'
            );
        }
    }

    /**
     * Instantiate the $model property to a Model representation based on the class provided.
     *
     * @return void
     */
    protected function initCrudModel()
    {
        if (!($this->model instanceof Model)) {
            $this->model = app($this->model);
        }
    }

    /**
     * Instantiate the $view property to a View representation based on the string provided.
     *
     * @return void
     */
    protected function initCrudView()
    {
        if (!($this->view instanceof View)) {
            $this->view = view($this->view);
        }
    }

    /**
     * Instantiate the $view property to a View representation based on the string provided.
     *
     * @return void
     */
    protected function initCrudRedirect()
    {
        if (!($this->redirect instanceof RedirectResponse)) {
            $this->redirect = redirect($this->redirect);
        }
    }

    /**
     * Instantiate the $view property to a View representation based on the string provided.
     *
     * @return void
     */
    protected function initCrudItems()
    {
        if (!($this->items instanceof Collection) && !($this->items instanceof LengthAwarePaginator)) {
            $this->items = collect($this->items);
        }
    }
}