<?php

namespace App\Http\Controllers\Front\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
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
     * Setup the page environment.
     *
     * @param Request $request
     * @set Page $page
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $action = $request->route()->action;

        if (isset($action['model']) && $action['model'] instanceof Page) {
            $this->page = $action['model'];

            if ($this->page->active != Page::ACTIVE_YES) {
                abort(404);
            }

            $this->page->load('layout');

            $this->setMeta([
                'title' => $this->page->meta_title ?: $this->page->name,
                'image' => $this->page->meta_image ? uploaded($this->page->meta_image)->url() : null,
                'description' => $this->page->description ?: null,
                'keywords' => $this->page->keywords ?: null,
            ]);

            view()->share([
                'page' => $this->page,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Dispatch to the correct controller action based on the page type.
     *
     * @return mixed
     */
    public function show()
    {
        return $this->{$this->page->route_action}();
    }

    /**
     * Method to execute when viewing a page of type "default".
     *
     * @return \Illuminate\View\View
     */
    public function normal()
    {
        return view($this->page->route_view);
    }

    /**
     * Method to execute when viewing a page of type "home".
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view($this->page->route_view);
    }
}