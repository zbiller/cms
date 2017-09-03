<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\AttributeFilter;
use App\Http\Requests\Shop\AttributeRequest;
use App\Http\Sorts\Shop\AttributeSort;
use App\Models\Shop\Attribute;
use App\Models\Shop\Attribute\Set;
use App\Models\Shop\Category;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
use Illuminate\Http\Request;

class AttributesController extends Controller
{
    use CanCrud;
    use CanOrder;

    /**
     * @var string
     */
    protected $model = Attribute::class;

    /**
     * @param Request $request
     * @param AttributeFilter $filter
     * @param AttributeSort $sort
     * @param Set $set
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AttributeFilter $filter, AttributeSort $sort, Set $set)
    {
        return $this->_index(function () use ($request, $filter, $sort, $set) {
            $query = Attribute::whereSet($set->id);

            if (count($request->all()) > 0) {
                $this->items = $query->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            } else {
                $this->items = $query->ordered()->get();
            }

            $this->title = 'Attributes';
            $this->view = view('admin.shop.attributes.index');
            $this->vars = [
                'set' => $set,
                'filters' => Attribute::$filters,
            ];
        });
    }

    /**
     * @param Set $set
     * @return \Illuminate\View\View
     */
    public function create(Set $set)
    {
        return $this->_create(function () use ($set) {
            $this->title = 'Add Attribute';
            $this->view = view('admin.shop.attributes.add');
            $this->vars = [
                'set' => $set,
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'filters' => Attribute::$filters,
            ];
        });
    }

    /**
     * @param AttributeRequest $request
     * @param Set $set
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AttributeRequest $request, Set $set)
    {
        return $this->_store(function () use ($request, $set) {
            $this->item = Attribute::create($request->all());
            $this->redirect = redirect()->route('admin.attributes.index', $set);

            $this->item->categories()->attach($request->input('categories'));
        }, $request);
    }

    /**
     * @param Set $set
     * @param Attribute $attribute
     * @return \Illuminate\View\View
     */
    public function edit(Set $set, Attribute $attribute)
    {
        return $this->_edit(function () use ($set, $attribute) {
            $this->item = $attribute;
            $this->title = 'Edit Attribute';
            $this->view = view('admin.shop.attributes.edit');
            $this->vars = [
                'set' => $set,
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'filters' => Attribute::$filters,
            ];
        });
    }

    /**
     * @param AttributeRequest $request
     * @param Attribute $attribute
     * @param Set $set
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AttributeRequest $request, Set $set, Attribute $attribute)
    {
        return $this->_update(function () use ($request, $set, $attribute) {
            $this->item = $attribute;
            $this->redirect = redirect()->route('admin.attributes.index', $set);

            $this->item->update($request->all());
            $this->item->categories()->sync($request->input('categories'));
        }, $request);
    }

    /**
     * @param Attribute $attribute
     * @param Set $set
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Set $set, Attribute $attribute)
    {
        return $this->_destroy(function () use ($set, $attribute) {
            $this->item = $attribute;
            $this->redirect = redirect()->route('admin.attributes.index', $set);

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param Set|null $set
     * @return array
     */
    public function get(Request $request, Set $set)
    {
        $result = [
            'items' => [],
        ];

        foreach ($set->attributes as $index => $attribute) {
            $result['items'][$index] = [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'values' => [],
            ];

            foreach ($attribute->values as $value) {
                $result['items'][$index]['values'][] = [
                    'id' => $value->id,
                    'value' => $value->value,
                ];
            }
        }

        return $result;
    }
}