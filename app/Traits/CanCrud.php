<?php

namespace App\Traits;

use DB;
use Route;
use Closure;
use Exception;
use App\Models\Model;
use App\Options\CrudOptions;
use App\Exceptions\CrudException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanCrud
{
    use ChecksTrait;

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\CrudOptions file.
     *
     * @var CrudOptions
     */
    protected static $crudOptions;

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
    protected static $methods = [
        'index' => 'GET',
        'deleted' => 'GET',
        'create' => 'GET',
        'edit' => 'GET',
        'store' => 'POST',
        'update' => 'PUT',
        'restore' => 'PUT',
        'destroy' => 'DELETE',
        'delete' => 'DELETE',
    ];

    /**
     * Instantiate the $CrudOptions property with the necessary crud properties.
     * Make necessary checks.
     *
     * @set $model
     * @throws CrudException
     */
    public static function bootCanCrud()
    {
        self::checkOptionsMethodDeclaration('getCrudOptions');

        self::$crudOptions = self::getCrudOptions();

        self::checkCrudMethod();
        self::checkCrudModel();
        self::checkCrudRoutes();
        self::checkCrudViews();
    }

    /**
     * This method should be called inside the controller's index() method.
     * The closure should at least set the $items collection.
     * $this->items = Model::get();
     *
     * @param Closure|null $function
     * @return \Illuminate\View\View
     */
    public function _index(Closure $function = null)
    {
        if ($function) {
            call_user_func($function);
        }

        $this->vars['items'] = $this->items;

        return view(self::$crudOptions->listView)->with($this->vars);
    }

    /**
     * This method should be called inside the controller's create() method.
     * The closure is totally optional. Although you could use it to assign variables to the view.
     * $this->vars['variable'] = $variable;
     *
     *
     * @param Closure|null $function
     * @return \Illuminate\View\View
     */
    public function _create(Closure $function = null)
    {
        if ($function) {
            call_user_func($function);
        }

        $this->vars['item'] = $this->item ?: self::$crudOptions->model;

        return view(self::$crudOptions->addView)->with($this->vars);
    }

    /**
     * This method should be called inside the controller's store() method.
     * The closure should at least create the database record.
     * $this->item = Model::create($request()->all());
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _store(Closure $function = null, Request $request = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully created!');
            return request()->has('save_stay') ? back() : redirect()->route(self::$crudOptions->listRoute);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return back()->withInput($request->all());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * This method should be called inside the controller's edit() method.
     * The closure should at least attempt to find the record in the database.
     * $this->item = Model::findOrFail($id);
     *
     * @param Closure|null $function
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function _edit(Closure $function = null)
    {
        try {
            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->item ?: self::$crudOptions->model;

            return view(self::$crudOptions->editView)->with($this->vars);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to access a record that does not exist!');
            return redirect()->route(self::$crudOptions->listRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        }
    }

    /**
     * This method should be called inside the controller's update() method.
     * The closure should at least attempt to find and update the record in the database.
     * $this->item = Model::findOrFail($id)->update($request->all());
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _update(Closure $function = null, Request $request = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully updated!');
            return request()->has('save_stay') ? back() : redirect()->route(self::$crudOptions->listRoute);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to update a record that does not exist!');
            return redirect()->route(self::$crudOptions->listRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return back()->withInput($request->all());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * This method should be called inside the controller's destroy() method.
     * The closure should at least attempt to find and delete the record from the database.
     * $this->item = Model::findOrFail($id)->delete();
     *
     * @param Closure|null $function
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _destroy(Closure $function = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully deleted!');
            return redirect()->route(self::$crudOptions->listRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to delete a record that does not exist!');
            return redirect()->route(self::$crudOptions->listRoute);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return back();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * This method should be called inside the controller's deleted() method.
     * The closure should at least set the $items collection.
     *
     * @param Closure|null $function
     * @return $this
     */
    public function _deleted(Closure $function = null)
    {
        if ($function) {
            call_user_func($function);
        }

        $this->vars['items'] = $this->items;

        return view(self::$crudOptions->deletedView)->with($this->vars);
    }

    /**
     * This method should be called inside the controller's restore() method.
     * The closure should at least attempt to restore the record in the database.
     * $this->item = Model::findOrFail($id)->restore($request->all());
     *
     * @param Closure|null $function
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _restore(Closure $function = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully restored!');
            return redirect()->route(self::$crudOptions->deletedRoute);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to restore a record that does not exist!');
            return redirect()->route(self::$crudOptions->deletedRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return redirect()->route(self::$crudOptions->deletedRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * This method should be called inside the controller's delete() method.
     * The closure should at least attempt to find and delete the record from the database.
     * $this->item = Model::findOrFail($id)->forceDelete();
     *
     * @param Closure|null $function
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _delete(Closure $function = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully deleted!');
            return redirect()->route(self::$crudOptions->deletedRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to delete a record that does not exist!');
            return redirect()->route(self::$crudOptions->deletedRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return redirect()->route(self::$crudOptions->deletedRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Verify if the current action runs under the appropriate CRUD method.
     *
     * @throws CrudException
     */
    private static function checkCrudMethod()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (isset(self::$methods[$action])) {
            if (request()->method() != self::$methods[$action]) {
                throw new CrudException(
                    'Action ' . $action . '() must use the ' . self::$methods[$action] . ' request method!'
                );
            }
        }
    }

    /**
     * Verify if the $model property has been properly set on the controller.
     * This property should contain the model class, as string.
     *
     * @throws CrudException
     */
    private static function checkCrudModel()
    {
        if (!(self::$crudOptions->model instanceof Model)) {
            throw new CrudException(
                'You must set the model via the getCrudOptions() method from controller.' . PHP_EOL .
                'Use the setModel() method from the App\Options\CrudOptions class.'
            );
        }
    }

    /**
     * Verify if the list|add|edit routes have been properly set on the controller.
     * These 3 properties should be set inside the getCrudOptions method.
     *
     * @throws CrudException
     */
    private static function checkCrudRoutes()
    {
        if (!self::$crudOptions->listRoute || !self::$crudOptions->addRoute || !self::$crudOptions->editRoute) {
            throw new CrudException(
                'You must set the listRoute, addRoute, editRoute via the getCrudOptions() method from controller.' . PHP_EOL .
                'Use the setListRoute(), setAddRoute(), setEditRoute() methods from the App\Options\CrudOptions class.'
            );
        }
    }

    /**
     * Verify if the list|add|edit views have been properly set on the controller.
     * These 3 properties should be set inside the getCrudOptions method.
     *
     * @throws CrudException
     */
    private static function checkCrudViews()
    {
        if (!self::$crudOptions->listView || !self::$crudOptions->addView || !self::$crudOptions->editView) {
            throw new CrudException(
                'You must set the listView, addView, editView via the getCrudOptions() method from controller.' . PHP_EOL .
                'Use the setListView(), setAddView(), setEditView() methods from the App\Options\CrudOptions class.'
            );
        }
    }
}