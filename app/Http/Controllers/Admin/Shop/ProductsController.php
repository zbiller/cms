<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Exceptions\UploadException;
use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use App\Models\Shop\Currency;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use App\Services\UploadService;
use App\Traits\CanCrud;
use App\Http\Requests\ProductRequest;
use App\Http\Filters\ProductFilter;
use App\Http\Sorts\ProductSort;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ProductsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Product::class;

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
    public function index(Request $request, ProductFilter $filter, ProductSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Product::with(['category', 'currency'])->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Products';
            $this->view = view('admin.shop.products.index');
            $this->vars = [
                'categories' => Category::alphabetically()->get(),
                'currencies' => Currency::alphabeticallyByCode()->get(),
                'actives' => Product::$actives,
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
                'categories' => Category::alphabetically()->get(),
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
                'categories' => Category::alphabetically()->get(),
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
}