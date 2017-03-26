<?php

namespace App\Http\Controllers\Admin\Cms;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Upload\Upload;
use App\Services\UploadService;
use App\Http\Requests\Crud\LibraryRequest;
use App\Http\Filters\Admin\LibraryFilter;
use App\Http\Sorts\Admin\LibrarySort;
use App\Traits\CanCrud;
use App\Options\CanCrudOptions;
use App\Exceptions\CrudException;
use App\Exceptions\UploadException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LibraryController extends Controller
{
    use CanCrud;

    /**
     * @param LibraryFilter $filter
     * @param LibrarySort $sort
     * @return \Illuminate\View\View
     */
    public function index(LibraryFilter $filter, LibrarySort $sort)
    {
        return $this->_index(function () use ($filter, $sort) {
            $this->items = Upload::filtered($filter)->sorted($sort)->paginate(10);

            $this->vars = [
                'types' => Upload::$types,
            ];
        });
    }

    /**
     * @param LibraryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LibraryRequest $request)
    {
        try {
            (new UploadService($request->file('file')))->upload();

            return [
                'status' => 'success',
                'msg' => 'Success',
                'code' => 501
            ];
        } catch (UploadException $e) {
            return [
                'status' => 'error',
                'msg' => $e->getMessage(),
                'code' => 403
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'msg' => 'Could not upload the file to library!',
                'code' => 403
            ];
        }

        return [
            'status' => 'error',
            'msg' => $e->getMessage(),
            'code' => 403
        ];




        return $this->_store(function () use ($request) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $this->item = app(Upload::class)->withUpload('asasa');

                $this->item->storeToDisk()->saveToDatabase();

            }
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        return $this->_destroy(function () use ($id) {
            try {
                $this->item = Upload::findOrFail($id);

                $this->item->withUpload($this->item->full_path);

                $this->item->deleteFromDatabase();
                $this->item->removeFromDisk();
            } catch (QueryException $e) {
                throw new CrudException('Cannot delete the file because it is used by other entities!');
            }
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        try {
            return Upload::findOrFail($id)->download();
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to download file that does not exist!');
            return redirect()->route('admin.library.index');
        }
    }

    /**
     * @return CanCrudOptions
     */
    public function getCanCrudOptions()
    {
        return CanCrudOptions::instance()
            ->setModel(app(Upload::class))
            ->setListRoute('admin.library.index')
            ->setListView('admin.cms.library.index')
            ->setAddRoute('admin.library.create')
            ->setAddView('admin.cms.library.add')
            ->setEditRoute('admin.library.edit')
            ->setEditView('admin.cms.library.edit');
    }
}