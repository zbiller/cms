<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Filters\Cms\PageFilter;
use App\Http\Requests\Cms\PageRequest;
use App\Http\Sorts\Cms\PageSort;
use App\Models\Cms\Layout;
use App\Models\Cms\Page;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\PreviewOptions;
use App\Options\RevisionOptions;
use App\Traits\CanCrud;
use App\Traits\CanDraft;
use App\Traits\CanDuplicate;
use App\Traits\CanPreview;
use App\Traits\CanRevision;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    use CanCrud;
    use CanDraft;
    use CanRevision;
    use CanPreview;
    use CanDuplicate;

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
                'parent' => $parent && $parent->exists ? $parent : null,
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
            $this->item = Page::create($request->all(), $parent && $parent->exists ? $parent : null);
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

    /**
     * Set the options for the CanDraft trait.
     *
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance()
            ->setEntityModel(Page::class)
            ->setValidatorRequest(new PageRequest)
            ->setFilterClass(new PageFilter)
            ->setSortClass(new PageSort)
            ->setListTitle('Drafted Pages')
            ->setSingleTitle('Page Draft')
            ->setListView('admin.cms.pages.drafts')
            ->setSingleView('admin.cms.pages.draft')
            ->setLimboView('admin.cms.pages.limbo')
            ->setRedirectUrl('admin.pages.drafts')
            ->setViewVariables([
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ]);
    }

    /**
     * Set the options for the CanRevision trait.
     *
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->setPageTitle('Page Revision')
            ->setPageView('admin.cms.pages.revision')
            ->setViewVariables([
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ]);
    }

    /**
     * Set the options for the CanPreview trait.
     *
     * @return PreviewOptions
     */
    public static function getPreviewOptions()
    {
        return PreviewOptions::instance()
            ->setEntityModel(Page::class)
            ->setValidatorRequest(new PageRequest);
    }

    /**
     * Set the options for the CanDuplicate trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->setEntityModel(Page::class)
            ->setRedirectUrl('admin.pages.edit');
    }
}