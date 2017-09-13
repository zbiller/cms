<?php

namespace App\Http\Controllers\Admin\Backup;

use App\Http\Controllers\Controller;
use App\Http\Filters\Backup\BackupFilter;
use App\Http\Sorts\Backup\BackupSort;
use App\Models\Backup\Backup;
use App\Traits\CanCrud;
use Artisan;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Storage;

class BackupsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Backup::class;

    /**
     * @param Request $request
     * @param BackupFilter $filter
     * @param BackupSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, BackupFilter $filter, BackupSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Backup::filtered($request, $filter);

            if ($request->filled('sort')) {
                $query->sorted($request, $sort);
            } else {
                $query->latest();
            }

            $this->items = $query->paginate(config('crud.per_page'));
            $this->title = 'Backups';
            $this->view = view('admin.backup.backups.index');
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            set_time_limit(300);
            ini_set('max_execution_time', 300);

            Artisan::call('backup:run');

            flash()->success('The record was successfully created!');
        } catch (Exception $e) {
            flash()->success($e->getMessage());
        }

        return redirect()->route('admin.backups.index');
    }

    /**
     * @param Backup $backup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Backup $backup)
    {
        try {
            return $backup->download();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to download a backup archive that does not exist!');
            return redirect()->route('admin.backups.index');
        }
    }

    /**
     * @param Backup $backup
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Backup $backup)
    {
        return $this->_destroy(function () use ($backup) {
            $this->redirect = redirect()->route('admin.backups.index');

            $filesystem = Storage::disk($backup->disk);

            if ($filesystem->exists($backup->path)) {
                $filesystem->delete($backup->path);
            }

            $backup->delete();
        });
    }
}