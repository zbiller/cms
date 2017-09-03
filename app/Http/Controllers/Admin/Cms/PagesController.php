<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Filters\Cms\PageFilter;
use App\Http\Requests\Cms\PageRequest;
use App\Http\Sorts\Cms\PageSort;
use App\Models\Cms\Layout;
use App\Models\Cms\Page;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Page::class;

    /**
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, PageFilter $filter, PageSort $sort)
    {
        cache()->forget('first_tree_load');

        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Page::whereIsRoot()->filtered($request, $filter);

            if ($request->filled('sort')) {
                $query->sorted($request, $sort);
            } else {
                $query->defaultOrder();
            }

            $this->items = $query->get();
            $this->title = 'Pages';
            $this->view = view('admin.cms.pages.index');
            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param Page $parent
     * @return \Illuminate\View\View
     */
    public function create(Page $parent = null)
    {
        return $this->_create(function () use ($parent) {
            $this->title = 'Add Page';
            $this->view = view('admin.cms.pages.add');
            $this->vars = [
                'parent' => $parent->exists ? $parent : null,
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param PageRequest $request
     * @param Page $parent
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(PageRequest $request, Page $parent = null)
    {
        return $this->_store(function () use ($request, $parent) {
            $this->item = Page::create($request->all(), $parent->exists ? $parent : null);
            $this->redirect = redirect()->route('admin.pages.index');
        }, $request);
    }

    /**
     * @param Page $page
     * @return \Illuminate\View\View
     */
    public function edit(Page $page)
    {
        return $this->_edit(function () use ($page) {
            $this->item = $page;
            $this->title = 'Edit Page';
            $this->view = view('admin.cms.pages.edit');
            $this->vars = [
                'layouts' => Layout::whereTypeIn(Page::$map[$page->type]['layouts'])->get(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param Page $page
     * @param PageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(PageRequest $request, Page $page)
    {
        return $this->_update(function () use ($page, $request) {
            $this->item = $page;
            $this->redirect = redirect()->route('admin.pages.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Page $page)
    {
        return $this->_destroy(function () use ($page) {
            $this->item = $page;
            $this->redirect = redirect()->route('admin.pages.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, PageFilter $filter, PageSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Page::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Deleted Pages';
            $this->view = view('admin.cms.pages.deleted');
            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
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
            $this->item = Page::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.pages.deleted');

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
            $this->item = Page::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.pages.deleted');

            $this->item->forceDelete();
        });
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function duplicate(Page $page)
    {
        return $this->_duplicate(function () use ($page) {
            $this->item = $page->saveAsDuplicate();
            $this->redirect = redirect()->route('admin.pages.edit', $this->item->id);
        });
    }

    /**
     * @param PageRequest $request
     * @param Page|null $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function preview(PageRequest $request, Page $page = null)
    {
        return $this->_preview(function () use ($page, $request) {
            if ($page && $page->exists) {
                $this->item = $page;
                $this->item->update($request->all());
            } else {
                $this->item = Page::create($request->all());
            }
        });
    }

    /**
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request, PageFilter $filter, PageSort $sort)
    {
        return $this->_drafts(function () use ($request, $filter, $sort) {
            $this->items = Page::onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Drafted Pages';
            $this->view = view('admin.cms.pages.drafts');
            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
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

            $this->title = 'Page Draft';
            $this->view = view('admin.cms.pages.draft');
            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
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
            $this->title = 'Page Draft';
            $this->view = view('admin.cms.pages.limbo');
            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        }, function () use ($request) {
            $this->item->saveAsDraft($request->all());
            $this->redirect = redirect()->route('admin.pages.drafts');
        }, $id, $request, new PageRequest());
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

            $this->title = 'Page Revision';
            $this->view = view('admin.cms.pages.revision');
            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        }, $revision);
    }

    /**
     * @param Request $request
     * @param int|null $type
     * @return array
     */
    public function layouts(Request $request, $type = null)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        if (isset(Page::$map[$type]['layouts']) && !empty(Page::$map[$type]['layouts'])) {
            $layouts = Layout::whereTypeIn(Page::$map[$type]['layouts'])->get();
            $result = [
                'status' => true,
                'items' => [],
            ];

            foreach ($layouts as $layout) {
                $result['items'][] = [
                    'id' => $layout->id,
                    'name' => $layout->name,
                ];
            }

            return response()->json($result);
        }

        return response()->json([
            'status' => false
        ]);
    }
}