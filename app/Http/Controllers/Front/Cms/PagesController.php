<?php

namespace App\Http\Controllers\Front\Cms;

use App\Models\Cms\Layout;
use App\Models\Cms\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * The loaded page model from the route parameter "model".
     *
     * @var Page
     */
    protected $page;

    /**
     * @param Request $request
     * @set Page $page
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $action = $request->route()->action;

        if (isset($action['model']) && $action['model'] instanceof Page) {
            $this->page = $action['model'];
            $this->page->load('layout');

            view()->share([
                'page' => $this->page,
                'layout' => $this->page->layout,
            ]);
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->page->routeView);
    }
}
