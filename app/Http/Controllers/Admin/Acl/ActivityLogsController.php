<?php

namespace App\Http\Controllers\Admin\Acl;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Auth\Activity;
use App\Models\Auth\User;
use App\Traits\CanCrud;
use App\Http\Filters\ActivityFilter;
use App\Http\Sorts\ActivitySort;
use Illuminate\Http\Request;

class ActivityLogsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Activity::class;

    /**
     * @param Request $request
     * @param ActivityFilter $filter
     * @param ActivitySort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, ActivityFilter $filter, ActivitySort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Activity::filtered($request, $filter);

            if ($request->has('sort')) {
                $query->sorted($request, $sort);
            } else {
                $query->newest();
            }

            $this->items = $query->paginate(10);
            $this->title = 'Activity Logs';
            $this->view = view('admin.acl.activity_logs.index');
            $this->vars = [
                'users' => User::alphabetically()->get(),
            ];
        });
    }

    /**
     * @param Activity $activity
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Activity $activity)
    {
        return $this->_destroy(function () use ($activity) {
            $this->item = $activity;
            $this->redirect = redirect()->route('admin.activity_logs.index');

            $this->item->delete();
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function clean()
    {
        try {
            Activity::clean();

            session()->flash('flash_success', 'The records were successfully cleaned up!');
        } catch (Exception $e) {
            session()->flash('flash_error', 'Could not clean up the records! Please try again.');
        }

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function delete()
    {
        try {
            Activity::truncate();

            session()->flash('flash_success', 'All records were successfully deleted!');
        } catch (Exception $e) {
            session()->flash('flash_error', 'Could not delete the records! Please try again.');
        }

        return back();
    }
}