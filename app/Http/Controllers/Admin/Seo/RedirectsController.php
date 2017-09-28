<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Controller;
use App\Http\Filters\Seo\RedirectFilter;
use App\Http\Requests\Seo\RedirectRequest;
use App\Http\Sorts\Seo\RedirectSort;
use App\Models\Seo\Redirect;
use App\Traits\CanCrud;
use Artisan;
use Exception;
use Illuminate\Http\Request;

class RedirectsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Redirect::class;

    /**
     * @param Request $request
     * @param RedirectFilter $filter
     * @param RedirectSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, RedirectFilter $filter, RedirectSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Redirect::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Redirects';
            $this->view = view('admin.seo.redirects.index');
            $this->vars = [
                'statuses' => Redirect::$statuses,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Redirect';
            $this->view = view('admin.seo.redirects.add');
            $this->vars = [
                'statuses' => Redirect::$statuses,
            ];
        });
    }

    /**
     * @param RedirectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(RedirectRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Redirect::create($request->all());
            $this->redirect = redirect()->route('admin.redirects.index');
        }, $request);
    }

    /**
     * @param Redirect $redirect
     * @return \Illuminate\View\View
     */
    public function edit(Redirect $redirect)
    {
        return $this->_edit(function () use ($redirect) {
            $this->item = $redirect;
            $this->title = 'Edit Redirect';
            $this->view = view('admin.seo.redirects.edit');
            $this->vars = [
                'statuses' => Redirect::$statuses,
            ];
        });
    }

    /**
     * @param RedirectRequest $request
     * @param Redirect $redirect
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(RedirectRequest $request, Redirect $redirect)
    {
        return $this->_update(function () use ($request, $redirect) {
            $this->item = $redirect;
            $this->redirect = redirect()->route('admin.redirects.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Redirect $redirect
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Redirect $redirect)
    {
        return $this->_destroy(function () use ($redirect) {
            $this->item = $redirect;
            $this->redirect = redirect()->route('admin.redirects.index');

            $this->item->delete();
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function find()
    {
        try {
            Artisan::call('link-checker:run');

            flash()->success(
                'Process finished successfully!<br /><br />' .
                'The broken links (if any) have been transformed into redirects.<br /><br />' .
                'Please note that you will have to manually set the url to redirect to, for all new broken links, as the "new url" is empty right now.'
            );
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.redirects.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clean()
    {
        try {
            Redirect::whereNull('new_url')->orWhereNotIn('status', array_keys(Redirect::$statuses))->delete();

            flash()->success('All bad or empty redirects have been successfully removed!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.redirects.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        try {
            Redirect::truncate();

            flash()->success('All redirects have been successfully removed!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.redirects.index');
    }
}