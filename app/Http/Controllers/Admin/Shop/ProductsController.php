<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Exceptions\UploadException;
use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\ProductFilter;
use App\Http\Requests\Shop\ProductRequest;
use App\Http\Sorts\Shop\ProductSort;
use App\Models\Shop\Attribute;
use App\Models\Shop\Attribute\Set;
use App\Models\Shop\Attribute\Value;
use App\Models\Shop\Category;
use App\Models\Shop\Currency;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use App\Models\Shop\Tax;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Services\UploadService;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
use DB;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use CanCrud;
    use CanOrder;

    /**
     * @var string
     */
    protected $model = Product::class;

    /**
     * @param Request $request
     * @param ProductFilter $filter
     * @param ProductSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, ProductFilter $filter, ProductSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $orderable = true;

            foreach ($request->except('category') as $input => $value) {
                $value = is_array($value) ? array_filter($value) : $value;

                if ($request->has('category') && !is_null($value) && !empty($value) && $value != '') {
                    $orderable = false;
                    break;
                }
            }

            if (!$request->get('category')) {
                $orderable = false;
            }

            $query = Product::with('category');

            if ($orderable) {
                $this->items = $query->whereCategory($request->get('category'))->ordered()->get();
            } else {
                $this->items = $query->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            }

            $this->title = 'Products';
            $this->view = view('admin.shop.products.index');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
                'orderable' => $orderable,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Product';
            $this->view = view('admin.shop.products.add');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
            ];
        });
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(ProductRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Product::create($request->all());
            $this->redirect = redirect()->route('admin.products.index');

            $this->item->categories()->attach($request->get('categories'));
        }, $request);
    }

    /**
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        return $this->_edit(function () use ($product) {
            $categories = Category::withDepth()->defaultOrder()->get();
            $sets = Set::ordered()->get();
            $attributes = $product->attributes()->get();
            $discounts = Discount::alphabetically()->active()->forProduct()->get();
            $taxes = Tax::alphabetically()->active()->forProduct()->get();
            $currencies = Currency::alphabeticallyByCode()->get();

            $this->item = $product;
            $this->title = 'Edit Product';
            $this->view = view('admin.shop.products.edit');
            $this->vars = [
                'categories' => $categories,
                'sets' => $sets,
                'attributes' => $attributes,
                'discounts' => $discounts,
                'taxes' => $taxes,
                'currencies' => $currencies,
                'actives' => Product::$actives,
                'inherits' => Product::$inherits,
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
            ];
        });
    }

    /**
     * @param ProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(ProductRequest $request, Product $product)
    {
        return $this->_update(function () use ($request, $product) {
            $this->item = $product;
            $this->redirect = redirect()->route('admin.products.index');

            $this->item->update($request->all());
            $this->item->categories()->sync($request->get('categories'));
        }, $request);
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        return $this->_destroy(function () use ($product) {
            $this->item = $product;
            $this->redirect = redirect()->route('admin.products.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $status = $message = $file = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            try {
                $upload = (new UploadService($request->file('file'), app(Product::class), 'metadata[images][*][image]'))->upload();

                $status = true;
                $file = $upload->getPath() . '/' . $upload->getName();
            } catch (UploadException $e) {
                $status = false;
                $message = $e->getMessage();
            } catch (Exception $e) {
                $status = false;
                $message = 'Could not upload the file!';
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'file' => $file,
        ]);
    }

    /**
     * @param Request $request
     * @param ProductFilter $filter
     * @param ProductSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, ProductFilter $filter, ProductSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Product::with('category')->onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Deleted Products';
            $this->view = view('admin.shop.products.deleted');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
            ];
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function restore($id)
    {
        return $this->_restore(function () use ($id) {
            $this->item = Product::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.products.deleted');

            $this->item->doNotGenerateUrl()->doNotSaveBlocks()->restore();
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->_delete(function () use ($id) {
            $this->item = Product::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.products.deleted');

            $this->item->forceDelete();
        });
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function duplicate(Product $product)
    {
        return $this->_duplicate(function () use ($product) {
            $this->item = $product->saveAsDuplicate();
            $this->redirect = redirect()->route('admin.products.edit', $this->item->id);
        });
    }

    /**
     * @param ProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function preview(ProductRequest $request, Product $product = null)
    {
        return $this->_preview(function () use ($product, $request) {
            if ($product && $product->exists) {
                $this->item = $product;
                $this->item->update($request->all());
            } else {
                $this->item = Product::create($request->all());
            }
        });
    }

    /**
     * @param Request $request
     * @param ProductFilter $filter
     * @param ProductSort $sort
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request, ProductFilter $filter, ProductSort $sort)
    {
        return $this->_drafts(function () use ($request, $filter, $sort) {
            $this->items = Product::with('category')->onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Drafted Products';
            $this->view = view('admin.shop.products.drafts');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
            ];
        });
    }

    /**
     * @param Draft $draft
     * @return \Illuminate\View\View
     */
    public function draft(Draft $draft)
    {
        return $this->_draft(function () use ($draft) {
            $this->item = $draft->draftable;
            $this->item->publishDraft($draft);

            $categories = Category::withDepth()->defaultOrder()->get();
            $sets = Set::ordered()->get();
            $attributes = $this->item->attributes()->get();
            $discounts = Discount::alphabetically()->active()->forProduct()->get();
            $taxes = Tax::alphabetically()->active()->forProduct()->get();
            $currencies = Currency::alphabeticallyByCode()->get();

            $this->title = 'Product Draft';
            $this->view = view('admin.shop.products.draft');
            $this->vars = [
                'categories' => $categories,
                'sets' => $sets,
                'attributes' => $attributes,
                'discounts' => $discounts,
                'taxes' => $taxes,
                'currencies' => $currencies,
                'actives' => Product::$actives,
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
            ];
        }, $draft);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function limbo(Request $request, $id)
    {
        return $this->_limbo(function () use ($id) {
            $product = Product::findOrFail($id);
            $categories = Category::withDepth()->defaultOrder()->get();
            $sets = Set::ordered()->get();
            $attributes = $product->attributes()->get();
            $discounts = Discount::alphabetically()->active()->forProduct()->get();
            $taxes = Tax::alphabetically()->active()->forProduct()->get();
            $currencies = Currency::alphabeticallyByCode()->get();

            $this->title = 'Product Draft';
            $this->view = view('admin.shop.products.limbo');
            $this->vars = [
                'categories' => $categories,
                'sets' => $sets,
                'attributes' => $attributes,
                'discounts' => $discounts,
                'taxes' => $taxes,
                'currencies' => $currencies,
                'actives' => Product::$actives,
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
            ];
        }, function () use ($request) {
            $this->item->saveAsDraft($request->all());
            $this->redirect = redirect()->route('admin.products.drafts');
        }, $id, $request, new ProductRequest());
    }

    /**
     * @param Revision $revision
     * @return \Illuminate\View\View
     */
    public function revision(Revision $revision)
    {
        return $this->_revision(function () use ($revision) {
            $this->item = $revision->revisionable;
            $this->item->rollbackToRevision($revision);

            $categories = Category::withDepth()->defaultOrder()->get();
            $sets = Set::ordered()->get();
            $attributes = $this->item->attributes()->get();
            $discounts = Discount::alphabetically()->active()->forProduct()->get();
            $taxes = Tax::alphabetically()->active()->forProduct()->get();
            $currencies = Currency::alphabeticallyByCode()->get();

            $this->title = 'Product Revision';
            $this->view = view('admin.shop.products.revision');
            $this->vars = [
                'categories' => $categories,
                'sets' => $sets,
                'attributes' => $attributes,
                'discounts' => $discounts,
                'taxes' => $taxes,
                'currencies' => $currencies,
                'actives' => Product::$actives,
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
            ];
        }, $revision);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAttribute(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'set_id' => 'required|numeric',
            'attribute_id' => 'required|numeric',
            'value_id' => 'nullable|numeric',
        ]);

        try {
            $set = Set::findOrFail($request->get('set_id'));
            $attribute = Attribute::findOrFail($request->get('attribute_id'));
            $value = Value::findOrFail($request->get('value_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                    'attribute_value' => $request->get('value') ?: $value->value,
                    'value_id' => $value->id,
                    'value' => $request->get('value') ?: '',
                    'url' => route('admin.attributes.edit', [$set, $attribute]),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadDiscount(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'discount_id' => 'required|numeric',
        ]);

        try {
            $discount = Discount::findOrFail($request->get('discount_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $discount->id,
                    'name' => $discount->name ?: 'N/A',
                    'rate' => $discount->rate ?: 'N/A',
                    'type' => isset(Discount::$types[$discount->type]) ? Discount::$types[$discount->type] : 'N/A',
                    'url' => route('admin.discounts.edit', $discount->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadTax(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'tax_id' => 'required|numeric',
        ]);

        try {
            $tax = Tax::findOrFail($request->get('tax_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $tax->id,
                    'name' => $tax->name ?: 'N/A',
                    'rate' => $tax->rate ?: 'N/A',
                    'type' => isset(Tax::$types[$tax->type]) ? Tax::$types[$tax->type] : 'N/A',
                    'url' => route('admin.taxes.edit', $tax->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveCustomAttributeValue(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'value_id' => 'required|numeric',
            'pivot_id' => 'required|numeric',
        ]);

        try {
            $value = Value::findOrFail($request->get('value_id'));
            $pivot = DB::table('product_attribute')->where('id', $request->get('pivot_id'));

            $pivot->update([
                'value' => $request->get('value') && $request->get('value') != $value->value ?
                    $request->get('value') : null
            ]);

            return response()->json([
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}