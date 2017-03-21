<?php

namespace App\Traits;

use DB;
use Route;
use Closure;
use Exception;
use App\Models\Model;
use App\Options\CanCrudOptions;
use App\Exceptions\CrudException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanCrud
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\CanCrudOptions file.
     *
     * @var CanCrudOptions
     */
    protected $canCrudOptions;

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
    protected $methods = [
        'index' => 'GET',
        'create' => 'GET',
        'edit' => 'GET',
        'store' => 'POST',
        'update' => 'PUT',
        'destroy' => 'DELETE',
    ];

    /**
     * The method used for setting the crud options.
     * This method should be called inside the controller using this trait.
     * Inside the method, you should set all the crud options.
     * This can be achieved using the methods from App\Options\CanCrudOptions.
     *
     * @return CanCrudOptions
     */
    abstract public function getCanCrudOptions(): CanCrudOptions;

    /**
     * Instantiate the $canCrudOptions property with the necessary crud properties.
     * Make necessary checks.
     *
     * @set $model
     * @throws CrudException
     */
    public function bootCanCrud()
    {
        $this->canCrudOptions = $this->getCanCrudOptions();

        $this->checkCrudMethod();
        $this->checkCrudModel();
        $this->checkCrudRoutes();
        $this->checkCrudViews();
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

        return view($this->canCrudOptions->listView)->with($this->vars);
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

        $this->vars['item'] = $this->item ?: $this->canCrudOptions->model;

        return view($this->canCrudOptions->addView)->with($this->vars);
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
            $this->request = $request;

            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully created!');
            return redirect()->route($request->has('save_stay') ? $this->canCrudOptions->addRoute : $this->canCrudOptions->listRoute);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return redirect()->back()->withInput($request->all());
        } catch (Exception $e) {
            if (env('APP_ENV') == 'development') {
                throw new Exception($e->getMessage());
            } else {
                session()->flash('flash_error', 'The record could not be created! Please try again.');
                return redirect()->back()->withInput($request->all());
            }
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

            $this->vars['item'] = $this->item ?: $this->canCrudOptions->model;

            return view($this->canCrudOptions->editView)->with($this->vars);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to access a record that does not exist!');
            return redirect()->route($this->canCrudOptions->listRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
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
            return redirect()->route($request->has('save_stay') ? $this->canCrudOptions->editRoute : $this->canCrudOptions->listRoute);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to update a record that does not exist!');
            return redirect()->route($this->canCrudOptions->listRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return redirect()->back()->withInput($request->all());
        } catch (Exception $e) {
            if (env('APP_ENV') == 'development') {
                throw new Exception($e->getMessage());
            } else {
                session()->flash('flash_error', 'The record could not be updated! Please try again.');
                return redirect()->back()->withInput($request->all());
            }
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
            return redirect()->route($this->canCrudOptions->listRoute, parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to delete a record that does not exist!');
            return redirect()->route($this->canCrudOptions->listRoute);
        } catch (CrudException $e) {
            session()->flash('flash_error', $e->getMessage());
            return redirect()->back();
        } catch (Exception $e) {
            if (env('APP_ENV') == 'development') {
                throw new Exception($e->getMessage());
            } else {
                session()->flash('flash_error', 'The record could not be deleted! Please try again.');
                return redirect()->back();
            }
        }
    }

    /**
     * Verify if the current action runs under the appropriate CRUD method.
     *
     * @throws CrudException
     */
    private function checkCrudMethod()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (isset($this->methods[$action])) {
            if (request()->method() != $this->methods[$action]) {
                throw new CrudException(
                    'Action ' . $action . '() must use the ' . $this->methods[$action] . ' request method!'
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
    private function checkCrudModel()
    {
        if (!($this->canCrudOptions->model instanceof Model)) {
            throw new CrudException(
                'You must set the model via the getCanCrudOptions() method from controller.' . PHP_EOL .
                'Use the setModel() method from the App\Options\CanCrudOptions class.'
            );
        }
    }

    /**
     * Verify if the list|add|edit routes have been properly set on the controller.
     * These 3 properties should be set inside the getCanCrudOptions method.
     *
     * @throws CrudException
     */
    private function checkCrudRoutes()
    {
        if (!$this->canCrudOptions->listRoute || !$this->canCrudOptions->addRoute || !$this->canCrudOptions->editRoute) {
            throw new CrudException(
                'You must set the listRoute, addRoute, editRoute via the getCanCrudOptions() method from controller.' . PHP_EOL .
                'Use the setListRoute(), setAddRoute(), setEditRoute() methods from the App\Options\CanCrudOptions class.'
            );
        }
    }

    /**
     * Verify if the list|add|edit views have been properly set on the controller.
     * These 3 properties should be set inside the getCanCrudOptions method.
     *
     * @throws CrudException
     */
    private function checkCrudViews()
    {
        if (!$this->canCrudOptions->listView || !$this->canCrudOptions->addView || !$this->canCrudOptions->editView) {
            throw new CrudException(
                'You must set the listView, addView, editView via the getCanCrudOptions() method from controller.' . PHP_EOL .
                'Use the setListView(), setAddView(), setEditView() methods from the App\Options\CanCrudOptions class.'
            );
        }
    }
}