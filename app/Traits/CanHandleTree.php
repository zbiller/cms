<?php

namespace App\Traits;

use Exception;
use App\Models\Model;
use App\Options\TreeOptions;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanHandleTree
{
    use ChecksTrait;

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\TreeOptions file.
     *
     * @var TreeOptions
     */
    protected static $treeOptions;

    /**
     * Instantiate the $treeOptions property with the necessary tree properties.
     *
     * @set $CanAuthenticateOptions
     */
    public static function bootCanHandleTree()
    {
        self::checkOptionsMethodDeclaration('getTreeOptions');

        self::$treeOptions = self::getTreeOptions();
    }

    /**
     * Fix the entire tree's lft and rgt.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixTree()
    {
        if (in_array('App\Traits\HasUrl', class_uses(self::$treeOptions->model))) {
            self::$treeOptions->model->doNotGenerateUrl();
        }

        self::$treeOptions->model->fixTree();

        return back();
    }

    /**
     * Load the model children of a selected tree node.
     *
     * @param int|null $parent
     * @return array
     * @throws Exception
     */
    public function loadTreeNodes($parent = null)
    {
        $data = [];

        if ($parent) {
            $items = self::$treeOptions->model->whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            $items = self::$treeOptions->model->whereIsRoot()->defaultOrder()->get();
            cache()->forget('first_tree_load');
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => self::$treeOptions->name,
                'children' => true,
                'type' => 'root',
                'icon' => 'jstree-folder'
            ];
        }

        if (isset($items)) {
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->{self::$treeOptions->text},
                    'children' => $item->{self::$treeOptions->children}->count() > 0 ? true : false,
                    'type' => 'child',
                    'icon' => 'jstree-folder'
                ];
            }
        }

        return $data;
    }

    /**
     * Get the items that should be displayed inside a tree node.
     * Return the list view displaying the items.
     *
     * @param Request $request
     * @param int|null $parent
     * @return mixed
     */
    public function listTreeItems(Request $request, $parent = null)
    {
        $query = self::$treeOptions->model->filtered($request, self::$treeOptions->filter);

        if ($request->has('sort')) {
            $query->sorted($request, self::$treeOptions->sort);
        } else {
            $query->defaultOrder();
        }

        try {
            $parent = self::$treeOptions->model->findOrFail($parent);

            $query->whereParent($parent->id);
        } catch (ModelNotFoundException $e) {
            $query->whereIsRoot();
        }

        $items = $query->get();

        return view(self::$treeOptions->view)->with([
            'items' => $items,
            'parent' => $parent,
        ])->with(self::$treeOptions->vars);
    }

    /**
     * Sort the tree nodes between themselves.
     *
     * @param Request $request
     * @return mixed
     */
    public function sortTreeItems(Request $request)
    {
        $tree = [];
        $branch = head($request->input('tree'))['children'];

        $this->rebuildTreeBranch($branch, $tree);

        if (in_array('App\Traits\HasUrl', class_uses(self::$treeOptions->model))) {
            self::$treeOptions->model->doNotGenerateUrl();
        }

        return self::$treeOptions->model->rebuildTree($tree);
    }

    /**
     * Re-build the urls of the tree node items.
     *
     * @param Request $request
     */
    public function refreshTreeItemsUrls(Request $request)
    {
        if (in_array('App\Traits\HasUrl', class_uses(self::$treeOptions->model))) {
            $data = $request->input('data');

            if ((int)$data['parent'] != (int)$data['old_parent']) {
                $parent = self::$treeOptions->model->find($data['parent']);
                $page = self::$treeOptions->model->find($data['page']);

                $page->url()->update([
                    'url' => trim(($parent ? $parent->url->url . '/' : '') . $page->{self::$treeOptions->slug}, '/')
                ]);

                $this->rebuildChildrenUrls($page);
            }
        }
    }

    /**
     * Rebuild tree nodes inside a parent node recursively.
     *
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
     * Re-build the children urls of a parent model.
     *
     * @param Model $parent
     */
    private function rebuildChildrenUrls(Model $parent)
    {
        foreach ($parent->children as $child) {
            $child->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : '') . $child->slug, '/')
            ]);

            $this->rebuildChildrenUrls($child);
        }
    }
}
