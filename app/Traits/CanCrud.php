<?php

namespace App\Traits;

use DB;
use Route;
use Closure;
use Exception;
use App\Models\Model;
use App\Http\Requests\Request;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanCrud
{
    /**
     * The model class name.
     * This same property should be defined on the controller.
     *
     * @var Model
     */
    protected $model;

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
     * Options for the list.
     * Should be an array containing the "route" and "view" for listing the entity.
     * Setting the $list should be done at controller level, via $list property.
     *
     * @var array
     */
    protected $list = [];

    /**
     * Options for the add.
     * Should be an array containing the "route" and "view" for adding the entity.
     * Setting the $add should be done at controller level, via $add property.
     *
     * @var array
     */
    protected $add = [];

    /**
     * Options for the edit.
     * Should be an array containing the "route" and "view" for editing the entity.
     * Setting the $edit should be done at controller level, via $edit property.
     *
     * @var array
     */
    protected $edit = [];

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
     * Make necessary checks.
     * Instantiate the model class.
     *
     * @set $model
     * @throws CrudException
     */
    public function __construct()
    {
        $this->checkMethod();
        $this->checkModel();
        $this->checkRoutesAndViews();

        if (!$this->model instanceof Model) {
            $this->model = app($this->model);
        }
    }

    /**
     * This method should be called inside the controller's index() method.
     * The closure should at least set the $items collection.
     * $this->items = $this->model->get();
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

        return view($this->list['view'])->with($this->vars);
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

        return view($this->add['view'])->with($this->vars);
    }

    /**
     * This method should be called inside the controller's store() method.
     * The closure should at least create the database record.
     * $this->item = $this->model->create($request()->all());
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function _store(Closure $function = null, Request $request = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully created!');
            return redirect()->route($this->list['route']);
        } catch (Exception $e) {
            session()->flash('flash_error', 'The record could not be created! Please try again.');
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * This method should be called inside the controller's edit() method.
     * The closure should at least attempt to find the record in the database.
     * $this->item = $this->model->findOrFail($id);
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

            $this->vars['item'] = $this->item;

            return view($this->edit['view'])->with($this->vars);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to access a record that does not exist!');
            return redirect()->route($this->list['route']);
        }
    }

    /**
     * This method should be called inside the controller's update() method.
     * The closure should at least attempt to find and update the record in the database.
     * $this->item = $this->model->findOrFail($id)->update($request->all());
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function _update(Closure $function = null, Request $request = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully updated!');
            return redirect()->route($this->list['route']);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to update a record that does not exist!');
            return redirect()->route($this->list['route']);
        } catch (Exception $e) {
            session()->flash('flash_error', 'The record could not be updated! Please try again.');
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * This method should be called inside the controller's destroy() method.
     * The closure should at least attempt to find and delete the record from the database.
     * $this->item = $this->model->findOrFail($id)->delete();
     *
     * @param Closure|null $function
     * @return \Illuminate\Http\RedirectResponse
     */
    public function _destroy(Closure $function = null)
    {
        try {
            if ($function) {
                DB::transaction($function);
            }

            session()->flash('flash_success', 'The record was successfully deleted!');
            return redirect()->route($this->list['route']);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to delete a record that does not exist!');
            return redirect()->route($this->list['route']);
        } catch (Exception $e) {
            session()->flash('flash_error', 'The record could not be deleted! Please try again.');
            return redirect()->back();
        }
    }

    /**
     * Verify if the current action runs under the appropriate CRUD method.
     *
     * @throws CrudException
     */
    private function checkMethod()
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
    private function checkModel()
    {
        if ($this->model === null) {
            throw new CrudException(
                get_class($this) . ' must have the protected property $model (string) !'
            );
        }
    }

    /**
     * Verify if the $list, $add, $edit have been properly set on the controller.
     * Each of these properties should be an array containing 2 keys: route and view.
     *
     * @throws CrudException
     */
    private function checkRoutesAndViews()
    {
        if (!isset($this->list['route']) || !isset($this->list['view'])) {
            throw new CrudException(
                get_class($this) . ' must have the protected property $list (array -> route & view)!'
            );
        }

        if (!isset($this->add['route']) || !isset($this->add['view'])) {
            throw new CrudException(
                get_class($this) . ' must have the protected property $add (array -> route & view)!'
            );
        }

        if (!isset($this->edit['route']) || !isset($this->edit['view'])) {
            throw new CrudException(
                get_class($this) . ' must have the protected property $edit (array -> route & view)!'
            );
        }
    }
}