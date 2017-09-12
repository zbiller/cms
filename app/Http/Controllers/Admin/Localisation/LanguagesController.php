<?php

namespace App\Http\Controllers\Admin\Localisation;

use App\Http\Controllers\Controller;
use App\Http\Filters\Localisation\LanguageFilter;
use App\Http\Requests\Localisation\LanguageRequest;
use App\Http\Sorts\Localisation\LanguageSort;
use App\Models\Localisation\Language;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Language::class;

    /**
     * @param Request $request
     * @param LanguageFilter $filter
     * @param LanguageSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, LanguageFilter $filter, LanguageSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Language::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Languages';
            $this->view = view('admin.localisation.languages.index');
            $this->vars = [
                'defaults' => Language::$defaults,
                'actives' => Language::$actives,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Language';
            $this->view = view('admin.localisation.languages.add');
            $this->vars = [
                'defaults' => Language::$defaults,
                'actives' => Language::$actives,
            ];
        });
    }

    /**
     * @param LanguageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(LanguageRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Language::create($request->all());
            $this->redirect = redirect()->route('admin.languages.index');
        }, $request);
    }

    /**
     * @param Language $language
     * @return \Illuminate\View\View
     */
    public function edit(Language $language)
    {
        return $this->_edit(function () use ($language) {
            $this->item = $language;
            $this->title = 'Edit Language';
            $this->view = view('admin.localisation.languages.edit');
            $this->vars = [
                'defaults' => Language::$defaults,
                'actives' => Language::$actives,
            ];
        });
    }

    /**
     * @param LanguageRequest $request
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(LanguageRequest $request, Language $language)
    {
        return $this->_update(function () use ($request, $language) {
            $this->item = $language;
            $this->redirect = redirect()->route('admin.languages.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Language $language)
    {
        return $this->_destroy(function () use ($language) {
            $this->item = $language;
            $this->redirect = redirect()->route('admin.languages.index');

            $this->item->delete();
        });
    }

    /**
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(Language $language)
    {
        session()->put('locale', $language->code);

        return back();
    }
}