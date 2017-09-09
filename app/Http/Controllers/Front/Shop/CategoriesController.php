<?php

namespace App\Http\Controllers\Front\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Models\Shop\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * The loaded category model from the route parameter "model".
     *
     * @var Category
     */
    protected $category;

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

        if (isset($action['model']) && $action['model'] instanceof Category) {
            $this->category = $action['model'];

            if (!($this->category && $this->category->exists && $this->category->active)) {
                abort(404);
            }

            $this->category->load('products');

            $this->page = page()->find('shop');

            if (!($this->page && $this->page->exists && $this->page->active)) {
                abort(404);
            }

            $this->page->load('layout');

            $this->setMeta([
                'title' => $this->category->meta_title ?: $this->category->name,
                'image' => $this->category->meta_image ? uploaded($this->category->meta_image)->url() : null,
                'description' => $this->category->description ?: null,
                'keywords' => $this->category->keywords ?: null,
            ]);

            view()->share([
                'category' => $this->category,
                'page' => $this->page,
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
        return view('front.shop.categories.view');
    }
}