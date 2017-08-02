<?php

namespace App\Traits;

use App\Exceptions\ActivityException;
use Exception;
use ReflectionMethod;
use App\Models\Auth\Activity;
use App\Options\ActivityOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasActivity
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\ActivityOptions file.
     *
     * @var ActivityOptions
     */
    protected static $activityLogOptions;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasActivity()
    {
        if (app()->runningInConsole() || config('activity.enabled') !== true) {
            return;
        }

        self::checkActivityOptions();

        self::$activityLogOptions = self::getActivityOptions();

        self::validateActivityOptions();

        static::getEventsToBeLogged()->each(function ($event) {
            return static::$event(function (Model $model) use ($event) {
                if (auth()->check()) {
                    activity_log()->causedBy(auth()->user())->performedOn($model)->log($model->getLogName($event));
                }
            });
        });
    }

    /**
     * Model has many activity logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Compose the log name.
     * By default, this method will return something like:
     *
     * {user_name} {action} {entity} {entity_name (optional)}
     * Example: Developer User created a page (Page Name)
     *
     * If you need different log name formats on different entities, you can override this method on the entity's model.
     * In the overwritten method you can define your own custom log name format.
     *
     * @param string|null $event
     * @return string
     */
    public function getLogName($event)
    {
        $user = auth()->check() ? auth()->user() : null;
        $name[] = $user && $user->exists ? $user->full_name : 'A user';

        if ($event && in_array(strtolower($event), array_map('strtolower', static::getEventsToBeLogged()->toArray()))) {
            if (strtolower($event) == 'deleted' && array_key_exists(SoftDeletes::class, class_uses($this))) {
                $event = ($this->forceDeleting ? 'force-' : 'soft-') . $event;
            }
        }

        $name[] = $event . ' a';
        $name[] = strtolower(last(explode('\\', get_class($this))));

        if (!empty($this->getAttributes())) {
            $name[] = '(' . $this->getAttribute(self::$activityLogOptions->field) . ')';
        }

        return implode(' ', $name);
    }

    /**
     * Get the event names that should be recorded.
     * If the events that should be recorded have been defined on the model, return only those.
     * Otherwise, return all Laravel's log compatible events.
     * The script will try to record an activity log for each of these events.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getEventsToBeLogged()
    {
        $events = self::$activityLogOptions->logEvents;

        if (isset($events) && is_array($events) && !empty($events)) {
            return collect($events);
        }

        $events = collect([
            'created', 'updated', 'deleted'
        ]);

        if (collect(class_uses(__CLASS__))->contains(SoftDeletes::class)) {
            $events->push('restored');
        }

        if (collect(class_uses(__CLASS__))->contains(HasDrafts::class)) {
            $events->push('drafted');
        }

        if (collect(class_uses(__CLASS__))->contains(HasRevisions::class)) {
            $events->push('revisioned');
        }

        if (collect(class_uses(__CLASS__))->contains(HasDuplicates::class)) {
            $events->push('duplicated');
        }

        return $events;
    }

    /**
     * Check if mandatory activity options have been properly set from the model.
     * Check if $field has been set.
     *
     * @return void
     * @throws ActivityException
     */
    protected static function validateActivityOptions()
    {
        if (!self::$activityLogOptions->field) {
            throw new ActivityException(
                'The model ' . self::class . ' uses the HasActivity trait' . PHP_EOL .
                'You are required to set the field that should act as the title for when logging activity.' . PHP_EOL .
                'You can do this from inside the getActivityOptions() method defined on the model.'
            );
        }
    }

    /**
     * Verify if the getActivityLogOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkActivityOptions()
    {
        if (!method_exists(self::class, 'getActivityOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getActivityOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getActivityOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getActivityOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}