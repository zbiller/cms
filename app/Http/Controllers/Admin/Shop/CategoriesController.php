<?php

namespace App\Http\Controllers\Admin\Shop;

use Exception;
use App\Models\Shop\Category;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Http\Controllers\Controller;
use App\Traits\CanCrud;
use App\Http\Requests\CategoryRequest;
use App\Http\Filters\CategoryFilter;
use App\Http\Sorts\CategorySort;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoriesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @param Request $request
     * @param CategoryFilter $filter
     * @param CategorySort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, CategoryFilter $filter, CategorySort $sort)
    {
        cache()->forget('first_tree_load');

        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Category::whereIsRoot()->filtered($request, $filter);

            if ($request->has('sort')) {
                $query->sorted($request, $sort);
            } else {
                $query->defaultOrder();
            }

            $this->items = $query->get();
            $this->title = 'Categories';
            $this->view = view('admin.shop.categories.index');
            $this->vars = [
                'actives' => Category::$actives,
            ];
        });
    }

    /**
     * @param Category $parent
     * @return \Illuminate\View\View
     */
    public function create(Category $parent = null)
    {
        return $this->_create(function () use ($parent) {
            $this->title = 'Add Category';
            $this->view = view('admin.shop.categories.add');
            $this->vars = [
                'parent' => $parent->exists ? $parent : null,
                'actives' => Category::$actives,
            ];
        });
    }

    /**
     * @param CategoryRequest $request
     * @param Category $parent
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CategoryRequest $request, Category $parent = null)
    {
        return $this->_store(function () use ($request, $parent) {
            $this->item = Category::create($request->all(), $parent->exists ? $parent : null);
            $this->redirect = redirect()->route('admin.categories.index');
        }, $request);
    }

    /**
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        return $this->_edit(function () use ($category) {
            $this->item = $category;
            $this->title = 'Edit Category';
            $this->view = view('admin.shop.categories.edit');
            $this->vars = [
                'actives' => Category::$actives,
            ];
        });
    }

    /**
     * @param Category $category
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CategoryRequest $request, Category $category)
    {
        return $this->_update(function () use ($category, $request) {
            $this->item = $category;
            $this->redirect = redirect()->route('admin.categories.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        return $this->_destroy(function () use ($category) {
            $this->item = $category;
            $this->redirect = redirect()->route('admin.categories.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param CategoryFilter $filter
     * @param CategorySort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, CategoryFilter $filter, CategorySort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Category::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Deleted Categories';
            $this->view = view('admin.shop.categories.deleted');
            $this->vars = [
                'actives' => Category::$actives,
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
            $this->item = Category::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.categories.deleted');

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
            $this->item = Category::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.categories.deleted');

            $this->item->forceDelete();
        });
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function duplicate(Category $category)
    {
        return $this->_duplicate(function () use ($category) {
            $this->item = $category->saveAsDuplicate();
            $this->redirect = redirect()->route('admin.categories.edit', $this->item->id);
        });
    }

    /**
     * @param CategoryRequest $request
     * @param Category|null $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function preview(CategoryRequest $request, Category $category = null)
    {
        return $this->_preview(function () use ($category, $request) {
            if ($category && $category->exists) {
                $this->item = $category;
                $this->item->update($request->all());
            } else {
                $this->item = Category::create($request->all());
            }
        });
    }

    /**
     * @param Request $request
     * @param CategoryFilter $filter
     * @param CategorySort $sort
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request, CategoryFilter $filter, CategorySort $sort)
    {
        return $this->_drafts(function () use ($request, $filter, $sort) {
            $this->items = Category::onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Drafted Categories';
            $this->view = view('admin.shop.categories.drafts');
            $this->vars = [
                'actives' => Category::$actives,
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

            $this->title = 'Category Draft';
            $this->view = view('admin.shop.categories.draft');
            $this->vars = [
                'actives' => Category::$actives,
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
            $this->title = 'Category Draft';
            $this->view = view('admin.shop.categories.limbo');
            $this->vars = [
                'actives' => Category::$actives,
            ];
        }, function () use ($request) {
            $this->item->saveAsDraft($request->all());
            $this->redirect = redirect()->route('admin.categories.drafts');
        }, $id, $request, new CategoryRequest());
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

            $this->title = 'Category Revision';
            $this->view = view('admin.shop.categories.revision');
            $this->vars = [
                'actives' => Category::$actives,
            ];
        }, $revision);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixTree()
    {
        app(Category::class)->doNotGenerateUrl()->doNotSaveBlocks()->fixTree();

        return back();
    }

    /**
     * @param int|null $parent
     * @return array
     * @throws \Exception
     */
    public function loadTreeNodes($parent = null)
    {
        $data = [];

        if ($parent) {
            $items = Category::whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            $items = Category::whereIsRoot()->defaultOrder()->get();
            cache()->forget('first_tree_load');
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => 'Categories',
                'children' => true,
                'type' => 'root',
                'icon' => 'jstree-folder'
            ];
        }

        if (isset($items)) {
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'children' => $item->children->count() > 0 ? true : false,
                    'type' => 'child',
                    'icon' => 'jstree-folder'
                ];
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param CategoryFilter $filter
     * @param CategorySort $sort
     * @param int|null $parent
     * @return \Illuminate\View\View
     */
    public function listTreeItems(Request $request, CategoryFilter $filter, CategorySort $sort, $parent = null)
    {
        $query = Category::filtered($request, $filter);

        if ($request->has('sort')) {
            $query->sorted($request, $sort);
        } else {
            $query->defaultOrder();
        }

        try {
            $parent = Category::findOrFail($parent);

            $query->whereParent($parent->id);
        } catch (ModelNotFoundException $e) {
            $query->whereIsRoot();
        }

        $items = $query->get();

        return view('admin.shop.categories._table')->with([
            'items' => $items,
            'parent' => $parent,
            'actives' => Category::$actives,
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function sortTreeItems(Request $request)
    {
        $tree = [];
        $branch = head($request->input('tree'))['children'];

        $this->rebuildTreeBranch($branch, $tree);

        return app(Category::class)->doNotGenerateUrl()->doNotSaveBlocks()->rebuildTree($tree);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function refreshTreeItemsUrls(Request $request)
    {
        $data = $request->input('data');

        if ((int)$data['parent'] != (int)$data['old_parent']) {
            $parent = Category::find($data['parent']);
            $category = Category::find($data['node']);

            $category->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : page()->find('shop')->url->url . '/') . $category->slug, '/')
            ]);

            $this->rebuildChildrenUrls($category->fresh(['url']));
        }
    }

    /**
     * @param array $items
     * @param array $array
     * @return void
     */
    private function rebuildTreeBranch(array $items, array &$array)
    {
        foreach ($items as $item) {
            if (!is_numeric($item['id'])) {
                continue;
            }

            $_item = [
                'id' => $item['id'],
                'name' => $item['text'],
            ];

            if (isset($item['children']) && is_array($item['children'])) {
                $_item['children'] = [];

                $this->rebuildTreeBranch($item['children'], $_item['children']);
            }

            $array[] = $_item;
        }
    }

    /**
     * @param Category $parent
     * @return void
     */
    private function rebuildChildrenUrls(Category $parent)
    {
        foreach ($parent->children as $child) {
            $child->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : '') . $child->slug, '/')
            ]);

            $this->rebuildChildrenUrls($child);
        }
    }
}