<?php

namespace App\Http\Controllers\Admin\Shop;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Models\Shop\Currency;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Services\UploadService;
use App\Traits\CanCrud;
use App\Traits\CanOrder;
use App\Http\Requests\ProductRequest;
use App\Http\Filters\ProductFilter;
use App\Http\Sorts\ProductSort;
use App\Exceptions\UploadException;
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
     * @var bool
     */
    protected $orderable = true;

    /**
     * @param Request $request
     * @param ProductFilter $filter
     * @param ProductSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, ProductFilter $filter, ProductSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->isOrderableOrNot($request);

            $query = Product::with(['category', 'currency']);

            if ($this->orderable) {
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
                'orderable' => $this->orderable,
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
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
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
            $this->items = Product::with(['category', 'currency'])->onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
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
            $this->items = Product::with(['category', 'currency'])->onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
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

            $this->title = 'Product Draft';
            $this->view = view('admin.shop.products.draft');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
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
        return $this->_limbo(function () {
            $this->title = 'Product Draft';
            $this->view = view('admin.shop.products.limbo');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
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

            $this->title = 'Product Revision';
            $this->view = view('admin.shop.products.revision');
            $this->vars = [
                'categories' => Category::withDepth()->defaultOrder()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
            ];
        }, $revision);
    }

    /**
     * @param Request $request
     * @return void
     */
    private function isOrderableOrNot(Request $request)
    {
        $this->orderable = true;

        foreach ($request->except('category') as $input => $value) {
            $value = is_array($value) ? array_filter($value) : $value;

            if ($request->has('category') && !is_null($value) && !empty($value) && $value != '') {
                $this->orderable = false;
                break;
            }
        }

        if (!$request->get('category')) {
            $this->orderable = false;
        }
    }
}