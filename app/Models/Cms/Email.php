<?php

namespace App\Models\Cms;

use App\Models\Model;
use App\Traits\HasDrafts;
use App\Traits\HasRevisions;
use App\Traits\HasDuplicates;
use App\Traits\HasActivity;
use App\Traits\HasMetadata;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\DraftOptions;
use App\Options\RevisionOptions;
use App\Options\DuplicateOptions;
use App\Options\ActivityOptions;
use App\Exceptions\EmailException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use HasDrafts;
    use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use HasMetadata;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use SoftDeletes;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'emails';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'identifier',
        'type',
        'metadata',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * The constants defining the email type.
     *
     * @const
     */
    const TYPE_PASSWORD_RECOVERY = 1;
    const TYPE_EMAIL_VERIFICATION = 2;

    /**
     * The property defining the email types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_PASSWORD_RECOVERY => 'Password Recovery',
        self::TYPE_EMAIL_VERIFICATION => 'Email Verification',
    ];

    /**
     * The options available for each email type.
     *
     * --- class
     * The mailable class used for sending the email.
     *
     * --- view
     * The blade file used for rendering the email.
     * The value here will be relative to the /resources/views directory.
     *
     * --- partial
     * The blade file used in admin for rendering custom email type fields.
     * All partial files should be inside the /resources/views/admin/cms/emails/partials directory.
     *
     * --- preview_image
     * The name of the image used as email type preview in admin.
     * All of the preview images should be placed inside /resources/assets/img/admin/emails directory.
     * Running "gulp" is required for migrating new images to the /public directory.
     *
     * --- variables
     * Array of variables that the respective mail type is allowed to use.
     * Each array item defined here, should represent a key from the public static $variables array defined below.
     *
     * @var array
     */
    public static $map = [
        self::TYPE_PASSWORD_RECOVERY => [
            'class' => 'App\Mail\PasswordRecovery',
            'view' => 'emails.password_recovery',
            'preview_image' => 'password_recovery.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'reset_password_url',
            ],
        ],
        self::TYPE_EMAIL_VERIFICATION => [
            'class' => 'App\Mail\EmailVerifications',
            'view' => 'emails.email_verification',
            'preview_image' => 'email_verification.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'email_verification_url',
            ],
        ],
    ];

    /**
     * All the available variables to be used inside mailables as dynamic content.
     * Each of these variables may belong to more that only one mail, but the implementation may differ inside each mailable class.
     *
     * --- name
     * The visual name of the variable.
     *
     * --- label
     * Short description of what the variable represents.
     *
     * --- description
     * Longer description of what the variable represents and how it works.
     *
     * @var array
     */
    public static $variables = [
        'first_name' => [
            'name' => 'First Name',
            'label' => 'The first name of the logged in user.',
            'description' => 'If used in an email, but no logged in user exists, this variable will not render anything.',
        ],
        'last_name' => [
            'name' => 'Last Name',
            'label' => 'The last name of the logged in user.',
            'description' => 'If used in an email, but no logged in user exists, this variable will not render anything.',
        ],
        'full_name' => [
            'name' => 'Full Name',
            'label' => 'The full name of the logged in user.',
            'description' => 'If used in an email, but no logged in user exists, this variable will not render anything.',
        ],
        'reset_password_url' => [
            'name' => 'Reset Password URL',
            'label' => 'The URL for resetting a user\'s password.',
            'description' => 'This URL will be generated dynamically based on users and their sessions.',
        ],
        'email_verification_url' => [
            'name' => 'Email Verification URL',
            'label' => 'The URL for verifying a user\'s email.',
            'description' => 'This URL will be generated dynamically based on the user\'s provided data upon registration.',
        ],
    ];

    /**
     * Get the from address of an email instance.
     *
     * @return mixed
     */
    public function getFromAddressAttribute()
    {
        if (isset($this->metadata->from_email)) {
            return $this->metadata->from_email;
        }

        return setting()->value('company-email') ?: config('mail.from.address');
    }

    /**
     * Get the from name of an email instance.
     *
     * @return mixed
     */
    public function getFromNameAttribute()
    {
        if (isset($this->metadata->from_name)) {
            return $this->metadata->from_name;
        }

        return setting()->value('company-name') ?: config('mail.from.name');
    }

    /**
     * Get the reply to address of an email instance.
     *
     * @return mixed
     */
    public function getReplyToAttribute()
    {
        if (isset($this->metadata->reply_to)) {
            return $this->metadata->reply_to;
        }

        return setting()->value('company-email') ?: config('mail.from.address');
    }

    /**
     * Get the subject of an email instance.
     *
     * @return mixed
     */
    public function getAttachmentAttribute()
    {
        if (isset($this->metadata->attachment)) {
            return $this->metadata->attachment;
        }

        return null;
    }

    /**
     * Get the subject of an email instance.
     *
     * @return mixed
     */
    public function getSubjectAttribute()
    {
        if (isset($this->metadata->subject)) {
            return $this->metadata->subject;
        }

        return null;
    }

    /**
     * Get the message of an email instance.
     *
     * @return mixed
     */
    public function getMessageAttribute()
    {
        if (isset($this->metadata->message)) {
            return $this->metadata->message;
        }

        return null;
    }

    /**
     * Sort the query with newest records first.
     *
     * @param Builder $query
     */
    public function scopeNewest($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Filter the query by the given identifier.
     *
     * @param Builder $query
     * @param string $identifier
     */
    public function scopeWhereIdentifier($query, $identifier)
    {
        $query->where('identifier', $identifier);
    }

    /**
     * Filter the query by the given type.
     *
     * @param Builder $query
     * @param string $type
     */
    public function scopeWhereType($query, $type)
    {
        $query->where('type', $type);
    }

    /**
     * Get the corresponding view from the $map property, for a loaded email instance.
     *
     * @return mixed
     * @throws EmailException
     */
    public function getView()
    {
        if (!isset(self::$map[$this->type]['view'])) {
            throw new EmailException('Email view not found!');
        }

        return self::$map[$this->type]['view'];
    }

    /**
     * Get the corresponding data for a loaded email instance.
     * Also, at the email data, append the additional provided data from this method.
     *
     * @param array $data
     * @return array
     */
    public function getData(array $data = [])
    {
        $original = isset($this->metadata) ? (array)$this->metadata : [];

        return array_merge($original, $data);
    }

    /**
     * Get the corresponding body variables for a email type.
     *
     * @param int $type
     * @return array
     */
    public static function getVariables($type)
    {
        $variables = [];

        if (!isset(self::$map[$type]['variables']) || empty(self::$map[$type]['variables'])) {
            return [];
        }

        foreach (self::$map[$type]['variables'] as $variable) {
            if (isset(self::$variables[$variable])) {
                $variables[$variable] = self::$variables[$variable];
            }
        }

        return $variables;
    }

    /**
     * Get the from email setting option.
     *
     * @return mixed
     */
    public static function getFromAddress()
    {
        return setting()->value('company-email') ?: config('mail.from.address');
    }

    /**
     * Get the from name setting option.
     *
     * @return mixed
     */
    public static function getFromName()
    {
        return setting()->value('company-name') ?: config('app.name');
    }

    /**
     * Get the formatted email types for a select.
     * Final format will be: type => image.
     *
     * @return array
     */
    public static function getImages()
    {
        $images = [];

        foreach (self::$map as $type => $options) {
            $images[$type] = $options['preview_image'];
        }

        return $images;
    }

    /**
     * Return the email corresponding to the provided identifier.
     *
     * @param string $identifier
     * @return Email
     * @throws EmailException
     */
    public static function findByIdentifier($identifier)
    {
        try {
            return Email::whereIdentifier($identifier)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new EmailException(
                'No email with the "' . $identifier . '" identifier was found!'
            );
        }
    }

    /**
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance();
    }

    /**
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(500);
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('name')
            ->excludeColumns('identifier');
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance();
    }
}