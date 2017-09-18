<?php

namespace App\Traits;

use App\Models\Model;
use App\Models\Version\Revision;
use App\Options\RevisionOptions;
use App\Services\CacheService;
use DB;
use Exception;
use Meta;
use ReflectionMethod;

trait CanRevision
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\RevisionOptions file.
     *
     * @var RevisionOptions
     */
    protected static $revisionOptions;

    /**
     * Instantiate the $revisionOptions property with the necessary revision properties.
     *
     * @set $AuthenticateOptions
     */
    public static function bootCanRevision()
    {
        self::checkRevisionOptions();

        self::$revisionOptions = self::getRevisionOptions();

        self::validateRevisionOptions();
    }

    /**
     * Display the revision view.
     * Set a back url in the session so we know where to redirect.
     * Set the revision page meta title.
     * Display the revision view.
     *
     * @param Revision $revision
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revision(Revision $revision)
    {
        $this->rememberRevisionBackUrl($revision);
        $this->establishRevisionPageTitle();

        try {
            DB::beginTransaction();
            CacheService::disableQueryCache();

            $model = $revision->revisionable;

            if (!$this->canBeRevisioned($model)) {
                flash()->error('This entity record cannot be revisioned!');
                return back();
            }

            $model->rollbackToRevision($revision);

            return $this->revisionViewWithVariables($model, $revision);
        } catch (Exception $e) {
            DB::rollBack();

            flash()->error('Could not display the revision! Please try again.');
            return back();
        }
    }

    /**
     * Verify if a model can be revisioned.
     * It has to use the App\Traits\HasRevisions trait.
     *
     * @param Model $model
     * @return bool
     */
    protected function canBeRevisioned(Model $model)
    {
        return $model && $model->exists && in_array(HasRevisions::class, class_uses($model));
    }

    /**
     * Remember the back url for when canceling, rolling back a revision.
     *
     * @param Revision $revision
     * @return void
     */
    protected function rememberRevisionBackUrl(Revision $revision)
    {
        if (!session('revision_back_url_' . $revision->id)) {
            session()->put('revision_back_url_' . $revision->id, url()->previous());
        }
    }

    /**
     * Set the meta title for the revision view page.
     *
     * @return void
     */
    protected function establishRevisionPageTitle()
    {
        $title = self::$revisionOptions->pageTitle;

        Meta::set('title', $title ? 'Admin - ' . $title : 'Admin');
    }

    /**
     * Build the revision view with every required or specified variable.
     *
     * @param Model $model
     * @param Revision $revision
     * @return \Illuminate\View\View
     */
    protected function revisionViewWithVariables(Model $model, Revision $revision)
    {
        return self::$revisionOptions->pageView->with(array_merge(
            self::$revisionOptions->viewVariables,
            ['item' => $model, 'revision' => $revision]
        ));
    }

    /**
     * Check if mandatory revision options have been properly set from the controller.
     * Check if $model has been properly set.
     * Check if $redirect has been properly set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validateRevisionOptions()
    {
        if (!self::$revisionOptions->pageView) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanRevision trait.' . PHP_EOL .
                'You are required to set the "page view" that will be returned when viewing a revision.' . PHP_EOL .
                'You can do this from inside the getRevisionOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the view must be an instance of Illuminate\View\View or a string.'
            );
        }
    }

    /**
     * Verify if the getRevisionOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkRevisionOptions()
    {
        if (!method_exists(self::class, 'getRevisionOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getRevisionOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getRevisionOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getRevisionOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}
