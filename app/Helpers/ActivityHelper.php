<?php

namespace App\Helpers;

use App\Models\Auth\Activity;
use App\Models\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ActivityHelper
{
    /**
     * Flag indicating whether or not activities should be logged throughout the app.
     *
     * @var bool
     */
    protected $logEnabled;

    /**
     * Number of days that an activity log should be kept.
     *
     * @var int
     */
    protected $clearOldRecords;

    /**
     * The Activity model class.
     *
     * @var Activity
     */
    protected $activity;

    /**
     * The entity the log will be registered for.
     *
     * @var Model
     */
    protected $subject;

    /**
     * The user causing the log triggering.
     *
     * @var Model
     */
    protected $causer;

    /**
     * The properties to be saved in a activity log.
     *
     * @var Collection
     */
    protected $properties;

    public function __construct()
    {
        $this->logEnabled = config('activity-log.enabled');
        $this->clearOldRecords = config('activity-log.delete_records_older_than');
        $this->activity = app(Activity::class);
    }

    /**
     * Set the subject that the activity log will be registered for.
     *
     * @param Model $model
     * @return $this
     */
    public function performedOn(Model $model)
    {
        $this->subject = $model;

        return $this;
    }

    /**
     * Set the user that will trigger the activity logging.
     *
     * @param Authenticatable $user
     * @return $this
     */
    public function causedBy(Authenticatable $user)
    {
        $this->causer = $user;

        return $this;
    }

    /**
     * Store the activity log into the database.
     * The $text parameter represents the name of the log.
     * Which is what the users will actually see when looking up a log.
     *
     * @param string $text
     * @return Activity
     */
    public function log($text)
    {
        if (app()->runningInConsole()) {
            return;
        }

        if (!$this->logEnabled) {
            return;
        }

        $this->activity->name = $text;

        if ($this->causer) {
            $this->activity->causer()->associate($this->causer);
        }

        if ($this->subject) {
            $this->activity->subject()->associate($this->subject);
        }

        $this->activity->save();

        return $this->activity;
    }
}