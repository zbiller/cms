<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Exceptions\UploadException;
use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\ProductFilter;
use App\Http\Requests\Shop\ProductRequest;
use App\Http\Sorts\Shop\ProductSort;
use App\Models\Localisation\Currency;
use App\Models\Shop\Attribute;
use App\Models\Shop\Attribute\Set;
use App\Models\Shop\Attribute\Value;
use App\Models\Shop\Category;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use App\Models\Shop\Tax;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\PreviewOptions;
use App\Options\RevisionOptions;
use App\Services\UploadService;
use App\Traits\CanCrud;
use App\Traits\CanDraft;
use App\Traits\CanDuplicate;
use App\Traits\CanOrder;
use App\Traits\CanPreview;
use App\Traits\CanRevision;
use DB;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use CanCrud;
    use CanDraft;
    use CanRevision;
    use CanPreview;
    use CanDuplicate;
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

                if ($request->filled('category') && !is_null($value) && !empty($value) && $value != '') {
                    $orderable = false;
                    break;
                }
            }

            if (!$request->query('category')) {
                $orderable = false;
            }

            $this->items = $orderable ?
                Product::with('category')->whereCategory($request->query('category'))->ordered()->get() :
                Product::with('category')->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));

            $this->title = 'Products';
            $this->view = view('admin.shop.products.index');
            $this->vars = array_merge(
                static::buildBasicViewVariables(),
                ['orderable' => $orderable]
            );
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
            $this->vars = static::buildAdvancedViewVariables();
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

            $this->saveProductPivotedRelations($request);
        }, $request);
    }

    /**
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        return $this->_edit(function () use ($product) {
            $this->item = $product;
            $this->title = 'Edit Product';
            $this->view = view('admin.shop.products.edit');
            $this->vars = static::buildAdvancedViewVariables();
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
            $this->saveProductPivotedRelations($request);
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
     * @param ProductFilter $filter
     * @param ProductSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, ProductFilter $filter, ProductSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Product::with('category')->onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Deleted Products';
            $this->view = view('admin.shop.products.deleted');
            $this->vars = static::buildBasicViewVariables();
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $status = $message = $file = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            try {
                $upload = (new UploadService(
                    $request->file('file'),
                    app(Product::class),
                    'metadata[images][*][image]'
                ))->upload();

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchChosen(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'query' => 'required',
        ]);

        $products = Product::inAlphabeticalOrder()->where('name', 'like', '%' . $request->query('query') . '%')->get();
        $results = [];

        if ($products->count() == 0) {
            $results[] = [
                'id' => '',
                'name' => 'No results match "' . $request->query('query') . '"',
                'disabled' => true,
            ];
        }

        foreach ($products as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'disabled' => false,
            ];
        }

        return response()->json($results);
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

        $request->validate([
            'set_id' => 'required|numeric',
            'attribute_id' => 'required|numeric',
            'value_id' => 'nullable|numeric',
        ]);

        try {
            $set = Set::findOrFail($request->input('set_id'));
            $attribute = Attribute::findOrFail($request->input('attribute_id'));
            $value = Value::findOrFail($request->input('value_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                    'attribute_value' => $request->input('value') ?: $value->value,
                    'value_id' => $value->id,
                    'value' => $request->input('value') ?: '',
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

        $request->validate([
            'discount_id' => 'required|numeric',
        ]);

        try {
            $discount = Discount::findOrFail($request->input('discount_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $discount->id,
                    'name' => $discount->name ?: 'N/A',
                    'rate' => $discount->rate ?: 'N/A',
                    'type' => Discount::$types[$discount->type] ?? 'N/A',
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

        $request->validate([
            'tax_id' => 'required|numeric',
        ]);

        try {
            $tax = Tax::findOrFail($request->input('tax_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $tax->id,
                    'name' => $tax->name ?: 'N/A',
                    'rate' => $tax->rate ?: 'N/A',
                    'type' => Tax::$types[$tax->type] ?? 'N/A',
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

        $request->validate([
            'value_id' => 'required|numeric',
            'pivot_id' => 'required|numeric',
        ]);

        try {
            $value = Value::findOrFail($request->input('value_id'));
            $pivot = DB::table('product_attribute')->where('id', $request->input('pivot_id'));

            $pivot->update([
                'value' => $request->filled('value') && $request->input('value') != $value->value ?
                    $request->input('value') : null
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

    /**
     * Set the options for the CanDraft trait.
     *
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance()
            ->setEntityModel(Product::class)
            ->setValidatorRequest(new ProductRequest)
            ->setFilterClass(new ProductFilter)
            ->setSortClass(new ProductSort)
            ->setListTitle('Drafted Products')
            ->setSingleTitle('Product Draft')
            ->setListView('admin.shop.products.drafts')
            ->setSingleView('admin.shop.products.draft')
            ->setLimboView('admin.shop.products.limbo')
            ->setRedirectUrl('admin.products.drafts')
            ->setViewVariables(static::buildAdvancedViewVariables());
    }

    /**
     * Set the options for the CanRevision trait.
     *
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->setPageTitle('Product Revision')
            ->setPageView('admin.shop.products.revision')
            ->setViewVariables(static::buildAdvancedViewVariables());
    }

    /**
     * Set the options for the CanPreview trait.
     *
     * @return PreviewOptions
     */
    public static function getPreviewOptions()
    {
        return PreviewOptions::instance()
            ->setEntityModel(Product::class)
            ->setValidatorRequest(new ProductRequest)
            ->withPivotedRelations([
                'categories' => 'categories',
                'attributes' => 'attributes',
                'discounts' => 'discounts',
                'taxes' => 'taxes',
            ]);
    }

    /**
     * Set the options for the CanDuplicate trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->setEntityModel(Product::class)
            ->setRedirectUrl('admin.products.edit');
    }

    /**
     * @param Request $request
     * @return void
     */
    protected function saveProductPivotedRelations(Request $request)
    {
        $this->item->categories()->sync($request->input('categories'));

        $this->item->attributes()->detach();
        $this->item->discounts()->detach();
        $this->item->taxes()->detach();

        foreach ((array)$request->input('attributes') as $index => $data) {
            foreach ($data as $id => $attributes) {
                $this->item->attributes()->attach($id, $attributes);
            }
        }

        foreach ((array)$request->input('discounts') as $index => $data) {
            foreach ($data as $id => $attributes) {
                $this->item->discounts()->attach($id, $attributes);
            }
        }

        foreach ((array)$request->input('taxes') as $index => $data) {
            foreach ($data as $id => $attributes) {
                $this->item->taxes()->attach($id, $attributes);
            }
        }
    }

    /**
     * @return array
     */
    protected static function buildBasicViewVariables()
    {
        return [
            'categories' => Category::withDepth()->defaultOrder()->get(),
            'currencies' => Currency::InAlphabeticalOrderByCode()->get(),
            'actives' => Product::$actives,
        ];
    }

    /**
     * @return array
     */
    protected static function buildAdvancedViewVariables()
    {
        return [
            'categories' => Category::withDepth()->defaultOrder()->get(),
            'sets' => Set::ordered()->get(),
            'discounts' => Discount::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
            'taxes' => Tax::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
            'currencies' => Currency::InAlphabeticalOrderByCode()->get(),
            'actives' => Product::$actives,
            'inherits' => Product::$inherits,
            'discountTypes' => Discount::$types,
            'taxTypes' => Tax::$types,
        ];
    }
}