<?php

namespace App\Options;

class ActivityOptions
{
    /**
     * The eloquent model events that should trigger an activity being logged.
     * By default (empty) all {after} model events will log activity.
     * created | updated | deleted | restored(*) | drafted(*)
     *
     * @var array
     */
    public $logEvents = [];

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
     * Set the $logEvents to work with in the App\Traits\HasActivityLog trait.
     *
     * @param ...$events
     * @return ActivityOptions
     */
    public function logOnlyForTheseEvents(...$events): ActivityOptions
    {
        $this->logEvents = array_flatten($events);

        return $this;
    }
}