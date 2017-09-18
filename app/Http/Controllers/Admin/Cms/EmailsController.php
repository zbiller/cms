<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Filters\Cms\EmailFilter;
use App\Http\Requests\Cms\EmailRequest;
use App\Http\Sorts\Cms\EmailSort;
use App\Models\Cms\Email;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\RevisionOptions;
use App\Traits\CanCrud;
use App\Traits\CanDraft;
use App\Traits\CanDuplicate;
use App\Traits\CanRevision;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;

class EmailsController extends Controller
{
    use CanCrud;
    use CanDraft;
    use CanRevision;
    use CanDuplicate;

    /**
     * @var string
     */
    protected $model = Email::class;

    /**
     * @param Request $request
     * @param EmailFilter $filter
     * @param EmailSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, EmailFilter $filter, EmailSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Email::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Emails';
            $this->view = view('admin.cms.emails.index');
            $this->vars = [
                'types' => Email::$types,
            ];
        });
    }

    /**
     * @param string|null $type
     * @return \Illuminate\View\View
     */
    public function create($type = null)
    {
        if (!$type || !array_key_exists($type, Email::$types)) {
            $this->setMeta('title', 'Admin - Add Email');

            return view('admin.cms.emails.init')->with([
                'title' => 'Add Email',
                'types' => Email::$types,
                'images' => Email::getImages(),
            ]);
        }

        return $this->_create(function () use ($type) {
            $this->title = 'Add Email';
            $this->view = view('admin.cms.emails.add');
            $this->vars = [
                'type' => $type,
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ];
        });
    }

    /**
     * @param EmailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(EmailRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Email::create($request->all());
            $this->redirect = redirect()->route('admin.emails.index');
        }, $request);
    }

    /**
     * @param Email $email
     * @return \Illuminate\View\View
     */
    public function edit(Email $email)
    {
        return $this->_edit(function () use ($email) {
            $this->item = $email;

            $this->title = 'Edit Email';
            $this->view = view('admin.cms.emails.edit');
            $this->vars = [
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ];
        });
    }

    /**
     * @param Email $email
     * @param EmailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(EmailRequest $request, Email $email)
    {
        return $this->_update(function () use ($email, $request) {
            $this->item = $email;
            $this->redirect = redirect()->route('admin.emails.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Email $email
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Email $email)
    {
        return $this->_destroy(function () use ($email) {
            $this->item = $email;
            $this->redirect = redirect()->route('admin.emails.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param EmailFilter $filter
     * @param EmailSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, EmailFilter $filter, EmailSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Email::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Deleted Emails';
            $this->view = view('admin.cms.emails.deleted');
            $this->vars = [
                'types' => Email::$types,
            ];
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function restore($id)
    {
        return $this->_restore(function () use ($id) {
            $this->item = Email::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.emails.deleted');

            $this->item->restore();
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->_delete(function () use ($id) {
            $this->item = Email::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.emails.deleted');

            $this->item->forceDelete();
        });
    }

    /**
     * @param EmailRequest $request
     * @param Email|null $email
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function preview(EmailRequest $request, Email $email = null)
    {
        try {
            DB::beginTransaction();

            if ($email && $email->exists) {
                $email->update($request->all());
            } else {
                $email = Email::create($request->all());
            }

            $view = Email::$map[$email->type]['view'];
            $data = (array)$email->metadata;

            DB::rollBack();

            return (new Markdown(view(), config('mail.markdown')))->render($view, $data);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the options for the CanDraft trait.
     *
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance()
            ->setEntityModel(Email::class)
            ->setValidatorRequest(new EmailRequest)
            ->setFilterClass(new EmailFilter)
            ->setSortClass(new EmailSort)
            ->setListTitle('Drafted Emails')
            ->setSingleTitle('Email Draft')
            ->setListView('admin.cms.emails.drafts')
            ->setSingleView('admin.cms.emails.draft')
            ->setLimboView('admin.cms.emails.limbo')
            ->setRedirectUrl('admin.emails.drafts')
            ->setViewVariables([
                'types' => Email::$types,
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ]);
    }

    /**
     * Set the options for the CanRevision trait.
     *
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->setPageTitle('Email Revision')
            ->setPageView('admin.cms.emails.revision')
            ->setViewVariables([
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ]);
    }

    /**
     * Set the options for the CanDuplicate trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->setEntityModel(Email::class)
            ->setRedirectUrl('admin.emails.edit');
    }
}