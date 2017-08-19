<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Http\Filters\Auth\ActivityFilter;
use App\Http\Sorts\Auth\ActivitySort;
use App\Models\Auth\Activity;
use App\Models\Auth\User;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class ActivityController extends Controller
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
                $query->latest();
            }

            $this->items = $query->paginate(10);
            $this->title = 'Activity';
            $this->view = view('admin.acl.activity.index');
            $this->vars = [
                'users' => User::inAlphabeticalOrder()->get(),
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
            $this->redirect = redirect()->route('admin.activity.index');

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

            flash()->success('The records were successfully cleaned up!');
        } catch (Exception $e) {
            flash()->error('Could not clean up the records! Please try again.');
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
            Activity::query()->delete();

            flash()->success('All records were successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Could not delete the records! Please try again.');
        }

        return back();
    }
}