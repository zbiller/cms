<?php

namespace App\Options;

use Exception;

class ActivityOptions
{
    /**
     * The database field of the model, the event should be logged with.
     * This field should contain the name, title or something similar for the entity.
     *
     * @var array
     */
    private $field = [];

    /**
     * The eloquent model events that should trigger an activity being logged.
     * By default (empty) all {after} model events will log activity.
     * created | updated | deleted | restored(*) | drafted(*)
     *
     * @var array
     */
    private $logEvents = [];

    /**
     * Get the value of a property of this class.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists(static::class, $name)) {
            return $this->{$name};
        }

        throw new Exception(
            'The property "' . $name . '" does not exist in class "' . static::class . '"'
        );
    }

    /**
     * Get a fresh instance of this class.
     *
     * @return ActivityOptions
     */
    public static function instance(): ActivityOptions
    {
        return new static();
    }

    /**
     * Set the $field to work with in the App\Traits\HasActivity trait.
     *
     * @param $field
     * @return ActivityOptions
     */
    public function logByField($field): ActivityOptions
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Set the $logEvents to work with in the App\Traits\HasActivity trait.
     *
     * @param $events
     * @return ActivityOptions
     */
    public function logOnlyForTheseEvents(...$events): ActivityOptions
    {
        $this->logEvents = array_flatten($events);

        return $this;
    }
}