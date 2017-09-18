<?php

namespace App\Traits;

use App\Models\Model;
use App\Options\DuplicateOptions;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use ReflectionMethod;

trait CanDuplicate
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\DuplicateOptions file.
     *
     * @var DuplicateOptions
     */
    protected static $duplicateOptions;

    /**
     * Instantiate the $duplicateOptions property with the necessary duplication properties.
     *
     * @set $AuthenticateOptions
     */
    public static function bootCanDuplicate()
    {
        self::checkDuplicateOptions();

        self::$duplicateOptions = self::getDuplicateOptions();

        self::validateDuplicateOptions();
    }

    /**
     * Duplicate the given entity record.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(Request $request, $id)
    {
        if (!$this->canBeDuplicated()) {
            flash()->error('This entity record cannot be duplicated!');
            return back();
        }

        try {
            $model = self::$duplicateOptions->entityModel;

            try {
                $model = $model->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                flash()->error('You are trying to duplicate a record that does not exist!');
                return back();
            }

            $duplicate = $model->saveAsDuplicate();

            flash()->success('The record was successfully duplicated!<br /><br />You have been redirected to the newly duplicated record.');
            return redirect()->route(self::$duplicateOptions->redirectUrl, $duplicate->id);
        } catch (Exception $e) {
            flash()->error('Failed duplicating the record!');
            return back()->withInput($request ? $request->all() : []);
        }
    }

    /**
     * Verify if a model can be duplicated.
     * It has to use the App\Traits\HasDuplicates trait.
     *
     * @return bool
     */
    protected function canBeDuplicated()
    {
        return in_array(HasDuplicates::class, class_uses(self::$duplicateOptions->entityModel));
    }

    /**
     * Check if mandatory duplicate options have been properly set from the controller.
     * Check if $model has been properly set.
     * Check if $redirect has been properly set.
     *
     * @return void
     * @throws Exception
     */
    protected static function validateDuplicateOptions()
    {
        if (!self::$duplicateOptions->entityModel || !(self::$duplicateOptions->entityModel instanceof Model)) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDuplicate trait.' . PHP_EOL .
                'You are required to set the "entity model" that will be duplicated.' . PHP_EOL .
                'You can do this from inside the getDuplicateOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the entity model must be an instance of App\Models\Model or a string.'
            );
        }

        if (!self::$duplicateOptions->redirectUrl) {
            throw new Exception(
                'The controller ' . self::class . ' uses the CanDuplicate trait.' . PHP_EOL .
                'You are required to set the "redirect" url to go to after the duplicate process happened.' . PHP_EOL .
                'You can do this from inside the getDuplicateOptions() method defined on the controller.' . PHP_EOL .
                'Please note that the redirect must a string representing the entity\'s edit route name (no parameters).'
            );
        }
    }

    /**
     * Verify if the getDuplicateOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkDuplicateOptions()
    {
        if (!method_exists(self::class, 'getDuplicateOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getDuplicateOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getDuplicateOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getDuplicateOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}
