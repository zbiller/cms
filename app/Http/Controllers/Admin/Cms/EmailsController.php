<?php

namespace App\Http\Controllers\Admin\Cms;

use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Cms\Email;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use App\Http\Filters\EmailFilter;
use App\Http\Sorts\EmailSort;
use App\Http\Requests\EmailRequest;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;

class EmailsController extends Controller
{
    use CanCrud;

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
            $this->items = Email::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
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
                'types' => Email::$types,
                'images' => Email::getImages(),
            ]);
        }

        return $this->_create(function () use ($type) {
            $this->title = 'Add Email';
            $this->view = view('admin.cms.emails.add');
            $this->vars = [
                'type' => $type,
                'partial' => Email::$map[$type]['partial'],
                'variables' => Email::getVariables($type),
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
                'partial' => $this->item->getPartial(),
                'variables' => Email::getVariables($this->item->type),
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
            $this->items = Email::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
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
     * @param Email $email
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function duplicate(Email $email)
    {
        return $this->_duplicate(function () use ($email) {
            $this->item = $email->saveAsDuplicate();
            $this->redirect = redirect()->route('admin.emails.edit', $this->item->id);
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
     * @param Request $request
     * @param EmailFilter $filter
     * @param EmailSort $sort
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request, EmailFilter $filter, EmailSort $sort)
    {
        return $this->_drafts(function () use ($request, $filter, $sort) {
            $this->items = Email::onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Drafted Emails';
            $this->view = view('admin.cms.emails.drafts');
            $this->vars = [
                'types' => Email::$types,
            ];
        });
    }

    /**
     * @param Draft $draft
     * @return \Illuminate\View\View
     */
    public function draft(Draft $draft)
    {
        return $this->_draft(function () use ($draft) {
            $this->item = $draft->draftable;
            $this->item->publishDraft($draft);

            $this->title = 'Email Draft';
            $this->view = view('admin.cms.emails.draft');
            $this->vars = [
                'partial' => $this->item->getPartial(),
                'variables' => Email::getVariables($this->item->type),
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ];
        }, $draft);
    }

    /**
     * @param EmailRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function limbo(EmailRequest $request, $id)
    {
        return $this->_limbo(function () {
            $this->title = 'Email Draft';
            $this->view = view('admin.cms.emails.limbo');
            $this->vars = [
                'partial' => $this->item->getPartial(),
                'variables' => Email::getVariables($this->item->type),
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ];
        }, function () use ($request) {
            $this->item->saveAsDraft($request->all());
            $this->redirect = redirect()->route('admin.emails.drafts');
        }, $id, $request);
    }

    /**
     * @param Revision $revision
     * @return \Illuminate\View\View
     */
    public function revision(Revision $revision)
    {
        return $this->_revision(function () use ($revision) {
            $this->item = $revision->revisionable;
            $this->item->rollbackToRevision($revision);

            $this->title = 'Email Revision';
            $this->view = view('admin.cms.emails.revision');
            $this->vars = [
                'partial' => $this->item->getPartial(),
                'variables' => Email::getVariables($this->item->type),
                'fromEmail' => Email::getFromAddress(),
                'fromName' => Email::getFromName(),
            ];
        }, $revision);
    }
}