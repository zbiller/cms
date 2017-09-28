<?php

namespace App\Http\Controllers\Admin\Upload;

use App\Exceptions\UploadException;
use App\Http\Controllers\Controller;
use App\Http\Filters\Upload\UploadFilter;
use App\Http\Sorts\Upload\UploadSort;
use App\Models\Upload\Upload;
use App\Services\UploadService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Image;
use Storage;
use Throwable;

class UploadsController extends Controller
{
    /**
     * @param Request $request
     * @param UploadFilter $filter
     * @param UploadSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, UploadFilter $filter, UploadSort $sort)
    {
        $items = Upload::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));

        $this->setMeta('title', 'Admin - Uploads');

        return view('admin.upload.uploads.index')->with([
            'title' => 'Uploads',
            'items' => $items,
            'types' => Upload::$types
        ]);
    }

    /**
     * @param Upload $upload
     * @return \Illuminate\Http\RedirectResponse
     * @throws UploadException
     */
    public function show(Upload $upload)
    {
        try {
            return (new UploadService($upload->full_path))->show();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to view a record that does not exist!');
            return redirect()->route('admin.uploads.index');
        }
    }

    /**
     * @param Request $request
     * @param array|string|int|null $type
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function get(Request $request, $type = null)
    {
        $uploads = Upload::latest()->onlyTypes($type)->onlyExtensions($request->query('accept'))->like([
            'original_name' => $request->query('keyword'),
        ])->paginate(28);

        return response()->json([
            'status' => $request->query('page') > 1 && !$uploads->count() ? false : true,
            'html' => $request->query('page') > 1 && !$uploads->count() ? '' : view('helpers::uploader.partials.items')->with([
                'type' => $type,
                'uploads' => $uploads,
            ])->render()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function set(Request $request)
    {
        try {
            if (!$request->filled('path') || !$request->filled('model') || !$request->filled('field')) {
                throw new Exception;
            }

            $upload = (new UploadService($request->input('path'), app($request->input('model')), $request->input('field')))->upload();

            return response()->json([
                'status' => true,
                'path' => $upload->getPath() . '/' . $upload->getName(),
                'name' => $upload->getOriginal()->original_name
            ]);
        } catch (UploadException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not set the file!',
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
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
                $message = 'Could not upload the file!';
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    /**
     * @param Upload $upload
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Upload $upload)
    {
        try {
            (new UploadService($upload->full_path))->unload();

            flash()->success('The record was successfully deleted!');
            return redirect()->route('admin.uploads.index', parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to delete a record that does not exist!');
            return redirect()->route('admin.uploads.index');
        } catch (QueryException $e) {
            flash()->error('Could not delete the file because it is used by other entities!');
            return redirect()->route('admin.uploads.index');
        } catch (UploadException $e) {
            flash()->error($e->getMessage());
            return back();
        } catch (Exception $e) {
            flash()->error('The record could not be deleted! Please try again.');
            return back();
        }
    }

    /**
     * @param Upload $upload
     * @return \Illuminate\Http\RedirectResponse
     * @throws UploadException
     */
    public function download(Upload $upload)
    {
        try {
            return (new UploadService($upload->full_path))->download();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to download a file that does not exist!');
            return redirect()->route('admin.uploads.index');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid file!'
            ]);
        }

        if ($request->input('accept') && !in_array($request->file('file')->getClientOriginalExtension(), explode(',', $request->input('accept')))) {
            return response()->json([
                'status' => false,
                'message' => 'File type is not allowed! Allowed extensions: ' . $request->input('accept')
            ]);
        }

        try {
            if (!$request->filled('model') || !$request->filled('field')) {
                throw new Exception;
            }

            $collection = new Collection();
            $file = (new UploadService($request->file('file'), app($request->input('model')), $request->input('field')))->upload();
            $upload = Upload::whereFullPath($file->getPath() . '/' . $file->getName())->firstOrFail();

            $collection->push($upload);

            return response()->json([
                'status' => true,
                'message' => 'Upload successful!',
                'type' => snake_case(Upload::$types[$file->getType()]),
                'html' => view('helpers::uploader.partials.items')->with([
                    'type' => snake_case(Upload::$types[$file->getType()]),
                    'uploads' => $collection,
                ])->render()
            ]);
        } catch (UploadException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Upload failed! Please try again.'
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function crop(Request $request)
    {
        $index = $request->query('index');
        $model = app($request->query('model'));
        $field = $request->query('field');
        $url = $request->query('url');
        $path = $request->query('path');
        $style = $request->query('style');

        if (isset($model->getUploadConfig()['images']['styles'])) {
            foreach ($model->getUploadConfig()['images']['styles'] as $name => $styles) {
                if (str_is($name, $field)) {
                    $field = $name;
                    break;
                }
            }
        }

        $width = array_get($model->getUploadConfig(), "images.styles.{$field}.{$style}.width");
        $height = array_get($model->getUploadConfig(), "images.styles.{$field}.{$style}.height");

        $imageSize = getimagesize(Storage::disk(config('upload.storage.disk'))->path($path));
        $cropSize = [$width, $height];
        $dCropSize = $cropSize;

        if ($dCropSize[0] && !$dCropSize[1]) {
            $dCropSize[1] = floor($dCropSize[0] / $imageSize[0] * $imageSize[1]);
        }

        if ($dCropSize[1] && !$dCropSize[0]) {
            $dCropSize[0] = floor($dCropSize[1] / $imageSize[1] * $imageSize[0]);
        }

        return response()->json([
            'status' => true,
            'html' => view('helpers::uploader.partials.crop')->with([
                'index' => $index,
                'url' => $url,
                'path' => $path,
                'style' => $style,
                'imageSize' => $imageSize,
                'cropSize' => $cropSize,
                'dCropSize' => $dCropSize,
            ])->render()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cut(Request $request)
    {
        try {
            (new UploadService(
                $request->input('path')
            ))->crop(
                $request->input('path'),
                $request->input('style'),
                $request->input('size'),
                $request->input('w'),
                $request->input('h'),
                $request->input('x'),
                $request->input('y')
            );

            return response()->json([
                'status' => true
            ]);
        } catch (UploadException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
            ]);
        }
    }
}