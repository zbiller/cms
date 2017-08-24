<?php

namespace App\Http\Controllers\Admin\Shop\Attributes;

use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\AttributeValueFilter;
use App\Http\Requests\Shop\AttributeValueRequest;
use App\Http\Sorts\Shop\AttributeValueSort;
use App\Models\Shop\Attribute;
use App\Models\Shop\Attribute\Set;
use App\Models\Shop\Attribute\Value;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
use Illuminate\Http\Request;

class ValuesController extends Controller
{
    use CanCrud;
    use CanOrder;

    /**
     * @var string
     */
    protected $model = Value::class;

    /**
     * @param Request $request
     * @param AttributeValueFilter $filter
     * @param AttributeValueSort $sort
     * @param Set $set
     * @param Attribute $attribute
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AttributeValueFilter $filter, AttributeValueSort $sort, Set $set, Attribute $attribute)
    {
        return $this->_index(function () use ($request, $filter, $sort, $set, $attribute) {
            $query = Value::whereAttribute($attribute->id);

            if (count($request->all()) > 0) {
                $this->items = $query->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            } else {
                $this->items = $query->ordered()->get();
            }

            $this->title = 'Values';
            $this->view = view('admin.shop.attributes.values.index');
            $this->vars = [
                'set' => $set,
                'attribute' => $attribute,
            ];
        });
    }

    /**
     * @param Set $set
     * @param Attribute $attribute
     * @return \Illuminate\View\View
     */
    public function create(Set $set, Attribute $attribute)
    {
        return $this->_create(function () use ($set, $attribute) {
            $this->title = 'Add Value';
            $this->view = view('admin.shop.attributes.values.add');
            $this->vars = [
                'set' => $set,
                'attribute' => $attribute,
            ];
        });
    }

    /**
     * @param AttributeValueRequest $request
     * @param Set $set
     * @param Attribute $attribute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AttributeValueRequest $request, Set $set, Attribute $attribute)
    {
        return $this->_store(function () use ($request, $set, $attribute) {
            $this->item = Value::create($request->all());
            $this->redirect = redirect()->route('admin.attribute_values.index', ['set' => $set, 'attribute' => $attribute]);
        }, $request);
    }

    /**
     * @param Set $set
     * @param Attribute $attribute
     * @param Value $value
     * @return \Illuminate\View\View
     */
    public function edit(Set $set, Attribute $attribute, Value $value)
    {
        return $this->_edit(function () use ($set, $attribute, $value) {
            $this->item = $value;
            $this->title = 'Edit Value';
            $this->view = view('admin.shop.attributes.values.edit');
            $this->vars = [
                'set' => $set,
                'attribute' => $attribute,
            ];
        });
    }

    /**
     * @param AttributeValueRequest $request
     * @param Set $set
     * @param Attribute $attribute
     * @param Value $value
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AttributeValueRequest $request, Set $set, Attribute $attribute, Value $value)
    {
        return $this->_update(function () use ($request, $set, $attribute, $value) {
            $this->item = $value;
            $this->redirect = redirect()->route('admin.attribute_values.index', ['set' => $set, 'attribute' => $attribute]);

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Set $set
     * @param Attribute $attribute
     * @param Value $value
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Set $set, Attribute $attribute, Value $value)
    {
        return $this->_destroy(function () use ($set, $attribute, $value) {
            $this->item = $value;
            $this->redirect = redirect()->route('admin.attribute_values.index', ['set' => $set, 'attribute' => $attribute]);

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param Set $set
     * @param Attribute $attribute
     * @return array
     */
    public function get(Request $request, Set $set, Attribute $attribute)
    {
        $result = [
            'items' => [],
        ];

        foreach ($attribute->values as $index => $value) {
            $result['items'][$index] = [
                'id' => $value->id,
                'value' => $value->value,
            ];
        }

        return $result;
    }
}