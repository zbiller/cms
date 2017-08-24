<?php

namespace App\Http\Controllers\Admin\Shop\Attributes;

use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\AttributeSetFilter;
use App\Http\Requests\Shop\AttributeSetRequest;
use App\Http\Sorts\Shop\AttributeSetSort;
use App\Models\Shop\Attribute\Set;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
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
     * @param AttributeSetFilter $filter
     * @param AttributeSetSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AttributeSetFilter $filter, AttributeSetSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            if (count($request->all()) > 0) {
                $this->items = Set::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            } else {
                $this->items = Set::ordered()->get();
            }

            $this->title = 'Attribute Sets';
            $this->view = view('admin.shop.attributes.sets.index');
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Attribute Set';
            $this->view = view('admin.shop.attributes.sets.add');
        });
    }

    /**
     * @param AttributeSetRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(AttributeSetRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Set::create($request->all());
            $this->redirect = redirect()->route('admin.attribute_sets.index');
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
            $this->view = view('admin.shop.attributes.sets.edit');
        });
    }

    /**
     * @param AttributeSetRequest $request
     * @param Set $set
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AttributeSetRequest $request, Set $set)
    {
        return $this->_update(function () use ($request, $set) {
            $this->item = $set;
            $this->redirect = redirect()->route('admin.attribute_sets.index');

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
            $this->redirect = redirect()->route('admin.attribute_sets.index');

            $this->item->delete();
        });
    }
}