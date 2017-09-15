<?php

namespace App\Http\Controllers\Admin\Translation;

use App\Exceptions\TranslationException;
use App\Http\Controllers\Controller;
use App\Http\Filters\Translation\TranslationFilter;
use App\Http\Requests\Translation\TranslationRequest;
use App\Http\Sorts\Translation\TranslationSort;
use App\Models\Localisation\Language;
use App\Models\Translation\Translation;
use App\Services\TranslationService;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class TranslationsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Translation::class;

    /**
     * @param Request $request
     * @param TranslationFilter $filter
     * @param TranslationSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, TranslationFilter $filter, TranslationSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Translation::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Translations';
            $this->view = view('admin.translation.translations.index');
            $this->vars = [
                'locales' => Language::onlyActive()->get()->pluck('name', 'code')->toArray(),
                'groups' => Translation::distinctGroup()->get()->pluck('group_formatted', 'group')->toArray(),
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Country';
            $this->view = view('admin.translation.translations.add');
        });
    }

    /**
     * @param TranslationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(TranslationRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Translation::create($request->all());
            $this->redirect = redirect()->route('admin.translations.index');
        }, $request);
    }

    /**
     * @param Translation $translation
     * @return \Illuminate\View\View
     */
    public function edit(Translation $translation)
    {
        return $this->_edit(function () use ($translation) {
            $this->item = $translation;
            $this->title = 'Edit Translation';
            $this->view = view('admin.translation.translations.edit');
        });
    }

    /**
     * @param TranslationRequest $request
     * @param Translation $translation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(TranslationRequest $request, Translation $translation)
    {
        return $this->_update(function () use ($request, $translation) {
            $this->item = $translation;
            $this->redirect = redirect()->route('admin.translations.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Translation $translation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Translation $translation)
    {
        return $this->_destroy(function () use ($translation) {
            $this->item = $translation;
            $this->redirect = redirect()->route('admin.translations.index');

            $this->item->delete();
        });
    }

    /**
     * @param TranslationService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(TranslationService $service)
    {
        try {
            $service->importAllTranslations();

            flash()->success('The translations have been successfully imported!');
        } catch (TranslationException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Could not import the translations! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @param TranslationService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(TranslationService $service)
    {
        try {
            $service->exportAllTranslations();

            flash()->success('The translations have been successfully exported!');
        } catch (TranslationException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Could not export the translations! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @param TranslationService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync(TranslationService $service)
    {
        try {
            $service->findMissingTranslations();

            flash()->success('The translations have been successfully synced!');
        } catch (TranslationException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Could not sync the missing translations if any! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        try {
            Translation::truncate();

            flash()->success('The translations have been successfully removed!');
        } catch (Exception $e) {
            flash()->error('Could remove the translations! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }
}