<?php

namespace App\Http\Controllers\Front\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Models\Shop\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * The loaded product model from the route parameter "model".
     *
     * @var Product
     */
    protected $product;

    /**
     * The loaded page model associated with the shop.
     * This is always the page with the identifier "shop".
     *
     * @var Page
     */
    protected $page;

    /**
     * Setup the page environment.
     *
     * @param Request $request
     * @set Page $page
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $action = $request->route()->action;

        if (isset($action['model']) && $action['model'] instanceof Product) {
            $this->product = $action['model'];

            if (!($this->product && $this->product->exists && $this->product->active)) {
                abort(404);
            }

            $this->product->load('category');

            $this->page = page()->find('shop');

            if (!($this->page && $this->page->exists && $this->page->active)) {
                abort(404);
            }

            $this->page->load('layout');

            $this->setMeta([
                'title' => $this->product->meta_title ?: $this->product->name,
                'image' => $this->product->meta_image ? uploaded($this->product->meta_image)->url() : null,
                'description' => $this->product->meta_description ?: null,
                'keywords' => $this->product->meta_keywords ?: null,
            ]);

            view()->share([
                'page' => $this->page,
                'product' => $this->product,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Method to execute when viewing a product.
     *
     * @return mixed
     */
    public function show()
    {
        return view('front.shop.products.view');
    }
}