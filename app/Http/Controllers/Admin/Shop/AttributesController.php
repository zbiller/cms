<?php

namespace App\Http\Controllers\Admin\Shop;

use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Shop\Attribute;
use App\Models\Shop\Product;
use App\Models\Shop\Set;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
use App\Http\Requests\AttributeRequest;
use App\Http\Filters\AttributeFilter;
use App\Http\Sorts\AttributeSort;
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
                $this->items = $query->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            } else {
                $this->items = $query->ordered()->get();
            }

            $this->title = 'Attributes';
            $this->view = view('admin.shop.attributes.index');
            $this->vars = [
                'set' => $set,
                'types' => Attribute::$types,
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
                'types' => Attribute::$types,
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
                'types' => Attribute::$types,
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
    public function get(Request $request, Set $set = null)
    {
        if ($set && $set->exists) {
            $result = [
                'items' => [],
            ];

            foreach ($set->attributes as $attribute) {
                $result['items'][] = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'value' => $attribute->value,
                ];
            }

            return $result;
        }

        $this->validate($request, [
            'product_id' => 'required|numeric',
        ]);

        try {
            $id = $request->get('product_id');
            $product = Product::withoutGlobalScopes()->findOrFail($id);

            if ($request->has('draft') && ($draft = Draft::find((int)$request->get('draft')))) {
                DB::beginTransaction();

                $product = $draft->draftable;
                $product->publishDraft($draft);
            }

            if ($request->has('revision') && ($revision = Revision::find((int)$request->get('revision')))) {
                DB::beginTransaction();

                $product = $revision->revisionable;
                $product->rollbackToRevision($revision);
            }

            return [
                'status' => true,
                'html' => view('admin.shop.attributes.assign.attributes')->with([
                    'product' => $product,
                    'sets' => Set::ordered()->get(),
                    'draft' => isset($draft) ? $draft : null,
                    'revision' => isset($revision) ? $revision : null,
                    'disabled' => $request->get('disabled') ? true : false,
                ])->render(),
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function row(Request $request)
    {
        $this->validate($request, [
            'set_id' => 'required|numeric',
            'attribute_id' => 'required|numeric',
        ]);

        try {
            $set = Set::findOrFail($request->get('set_id'));
            $attribute = Attribute::findOrFail($request->get('attribute_id'));

            return [
                'status' => true,
                'data' => [
                    'id' => $attribute->id,
                    'name' => $attribute->name ?: 'N/A',
                    'value' => $attribute->value ?: 'N/A',
                    'val' => $request->get('val') ?: '',
                    'url' => route('admin.attributes.edit', ['set' => $set->id, 'id' => $attribute->id]),
                ],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function value(Request $request)
    {
        $this->validate($request, [
            'attribute_id' => 'required|numeric',
            'pivot_id' => 'required|numeric',
        ]);

        try {
            $attribute = Attribute::findOrFail($request->get('attribute_id'));
            $pivot = DB::table('product_attribute')->where('id', $request->get('pivot_id'));

            $pivot->update([
                'val' => $request->get('value') && $request->get('value') != $attribute->value ?
                    $request->get('value') : null
            ]);

            return [
                'status' => true
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}