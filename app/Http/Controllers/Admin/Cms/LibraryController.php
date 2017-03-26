<?php

namespace App\Http\Controllers\Admin\Cms;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Upload\Upload;
use App\Services\UploadService;
use App\Http\Requests\Crud\LibraryRequest;
use App\Http\Filters\Admin\LibraryFilter;
use App\Http\Sorts\Admin\LibrarySort;
use App\Exceptions\UploadException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LibraryController extends Controller
{
    /**
     * @param LibraryFilter $filter
     * @param LibrarySort $sort
     * @return \Illuminate\View\View
     */
    public function index(LibraryFilter $filter, LibrarySort $sort)
    {
        $items = Upload::filtered($filter)->sorted($sort)->paginate(10);

        return view('admin.cms.library.index')->with([
            'items' => $items,
            'types' => Upload::$types
        ]);
    }

    /**
     * @param LibraryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LibraryRequest $request)
    {
        $status = $message = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            try {
                (new UploadService($request->file('file')))->upload();

                $status = true;
            } catch (UploadException $e) {
                $status = false;
                $message = $e->getMessage();
            } catch (Exception $e) {
                $status = false;
                $message = 'Could not upload the file to library!';
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            (new UploadService(Upload::findOrFail($id)->full_path))->unload();

            session()->flash('flash_success', 'The record was successfully deleted!');
            return redirect()->route('admin.library.index', parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to delete a record that does not exist!');
            return redirect()->route('admin.library.index');
        } catch (QueryException $e) {
            session()->flash('flash_error', 'Could not delete the file because it is used by other entities!');
            return redirect()->route('admin.library.index');
        } catch (Exception $e) {
            session()->flash('flash_error', 'The record could not be deleted! Please try again.');
            return back();
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        try {
            return (new UploadService(Upload::findOrFail($id)->full_path))->download();
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to download file that does not exist!');
            return redirect()->route('admin.library.index');
        }
    }
}