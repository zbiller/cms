<?php

namespace App\Http\Controllers\Admin\Version;

use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Version\Revision;
use Illuminate\Http\Request;

class RevisionsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'revisionable_id' => 'required|numeric',
            'revisionable_type' => 'required',
            'route' => 'required',
        ]);

        try {
            $route = $request->get('route');
            $revisions = Revision::with('user')->whereRevisionable(
                $request->get('revisionable_id'),
                $request->get('revisionable_type')
            )->newest()->get();

            return [
                'status' => true,
                'html' => view('helpers::revision.partials.table')->with([
                    'revisions' => $revisions,
                    'route' => $route,
                ])->render()
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
            ];
        }
    }

    /**
     * @param Revision $revision
     * @return array|mixed
     */
    public function rollback(Revision $revision)
    {
        try {
            $revision->revisionable->rollbackToRevision($revision);
            session()->flash('flash_success', 'The record was successfully rolled back to the specified revision!');

            return request()->ajax() ?
                ['status' => true] :
                redirect(session('revision_rollback_url') ?: url()->previous());
        } catch (Exception $e) {
            return [
                'status' => false,
            ];
        }
    }

    /**
     * @param Request $request
     * @param Revision $revision
     * @return array|mixed
     */
    public function destroy(Request $request, Revision $revision)
    {
        $this->validate($request, [
            'revisionable_id' => 'required|numeric',
            'revisionable_type' => 'required',
        ]);

        try {
            return DB::transaction(function () use ($request, $revision) {
                $revision->delete();

                $route = $request->get('route');
                $revisions = Revision::with('user')->whereRevisionable(
                    $request->get('revisionable_id'),
                    $request->get('revisionable_type')
                )->newest()->get();

                return [
                    'status' => true,
                    'html' => view('helpers::revision.partials.table')->with([
                        'revisions' => $revisions,
                        'route' => $route,
                    ])->render()
                ];
            });
        } catch (Exception $e) {
            return [
                'status' => false,
            ];
        }
    }
}