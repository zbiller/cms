<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Set;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
use App\Http\Requests\SetRequest;
use App\Http\Filters\SetFilter;
use App\Http\Sorts\SetSort;
use Illuminate\Http\Request;

class SetsController extends Controller
{
    use CanCrud;
    use CanOrder;

    /**
     * @var string
     */
    protected $model = Set::class;

    /**
     * @param Request $request
     * @param SetFilter $filter
     * @param SetSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, SetFilter $filter, SetSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            if (count($request->all()) > 0) {
                $this->items = Set::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            } else {
                $this->items = Set::ordered()->get();
            }

            $this->title = 'Attribute Sets';
            $this->view = view('admin.shop.sets.index');
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Attribute Set';
            $this->view = view('admin.shop.sets.add');
        });
    }

    /**
     * @param SetRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(SetRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Set::create($request->all());
            $this->redirect = redirect()->route('admin.sets.index');
        }, $request);
    }

    /**
     * @param Set $set
     * @return \Illuminate\View\View
     */
    public function edit(Set $set)
    {
        return $this->_edit(function () use ($set) {
            $this->item = $set;
            $this->title = 'Edit Attribute Set';
            $this->view = view('admin.shop.sets.edit');
        });
    }

    /**
     * @param SetRequest $request
     * @param Set $set
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(SetRequest $request, Set $set)
    {
        return $this->_update(function () use ($request, $set) {
            $this->item = $set;
            $this->redirect = redirect()->route('admin.sets.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Set $set
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Set $set)
    {
        return $this->_destroy(function () use ($set) {
            $this->item = $set;
            $this->redirect = redirect()->route('admin.sets.index');

            $this->item->delete();
        });
    }
}