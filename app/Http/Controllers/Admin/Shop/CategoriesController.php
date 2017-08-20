<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\CategoryFilter;
use App\Http\Requests\Shop\CategoryRequest;
use App\Http\Sorts\Shop\CategorySort;
use App\Models\Shop\Category;
use App\Models\Shop\Discount;
use App\Models\Shop\Tax;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

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
                'discounts' => Discount::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'taxes' => Tax::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
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
            $this->redirect = redirect()->route('admin.product_categories.index');
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
                'discounts' => Discount::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'taxes' => Tax::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
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
            $this->redirect = redirect()->route('admin.product_categories.index');

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
            $this->redirect = redirect()->route('admin.product_categories.index');

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
            $this->redirect = redirect()->route('admin.product_categories.deleted');

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
            $this->redirect = redirect()->route('admin.product_categories.deleted');

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
            $this->redirect = redirect()->route('admin.product_categories.edit', $this->item->id);
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
                'discounts' => Discount::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'taxes' => Tax::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
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
                'discounts' => Discount::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'taxes' => Tax::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
            ];
        }, function () use ($request) {
            $this->item->saveAsDraft($request->all());
            $this->redirect = redirect()->route('admin.product_categories.drafts');
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
                'discounts' => Discount::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'taxes' => Tax::inAlphabeticalOrder()->onlyActive()->forProduct()->get(),
                'discountTypes' => Discount::$types,
                'taxTypes' => Tax::$types,
            ];
        }, $revision);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadDiscount(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'discount_id' => 'required|numeric',
        ]);

        try {
            $discount = Discount::findOrFail($request->get('discount_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $discount->id,
                    'name' => $discount->name ?: 'N/A',
                    'rate' => $discount->rate ?: 'N/A',
                    'type' => isset(Discount::$types[$discount->type]) ? Discount::$types[$discount->type] : 'N/A',
                    'url' => route('admin.discounts.edit', $discount->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadTax(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'tax_id' => 'required|numeric',
        ]);

        try {
            $tax = Tax::findOrFail($request->get('tax_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $tax->id,
                    'name' => $tax->name ?: 'N/A',
                    'rate' => $tax->rate ?: 'N/A',
                    'type' => isset(Tax::$types[$tax->type]) ? Tax::$types[$tax->type] : 'N/A',
                    'url' => route('admin.taxes.edit', $tax->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}