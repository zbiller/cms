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

        if (isset($action['model']) && $action['model'] instanceof Page && ($this->page = $action['model'])) {
            if ($this->page->active != Page::ACTIVE_YES) {
                abort(404);
            }

            $this->page->load('layout');
            $this->setPageMeta();

            view()->share([
                'page' => $this->page,
                'layout' => $this->page->layout,
            ]);
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
     * Method to execute when viewing a page of type "normal".
     *
     * @return \Illuminate\View\View
     */
    public function normal()
    {
        return view($this->page->route_view);
    }

    /**
     * Method to execute when viewing a page of type "special".
     *
     * @return \Illuminate\View\View
     */
    public function custom()
    {
        return view($this->page->route_view);
    }

    /**
     * Build the meta tags for the page.
     *
     * @return void
     */
    private function setPageMeta()
    {
        $this->setMeta('title', isset($this->page->metadata->meta->title) ? $this->page->metadata->meta->title : $this->page->name);

        if (isset($this->page->metadata->meta) && $meta = $this->page->metadata->meta) {
            $this->setMeta([
                'image' => isset($meta->image) ? uploaded($meta->image)->url() : null,
                'description' => isset($meta->description) ? $meta->description : null,
                'keywords' => isset($meta->keywords) ? $meta->keywords : null,
            ]);
        }
    }
}