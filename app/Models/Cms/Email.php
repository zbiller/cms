<?php

namespace App\Models\Cms;

use App\Exceptions\EmailException;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\RevisionOptions;
use App\Traits\HasActivity;
use App\Traits\HasDrafts;
use App\Traits\HasDuplicates;
use App\Traits\HasMetadata;
use App\Traits\HasRevisions;
use App\Traits\HasUploads;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use HasUploads;
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
        'deleted_at',
        'drafted_at',
    ];

    /**
     * The constants defining the email type.
     *
     * @const
     */
    const TYPE_PASSWORD_RECOVERY = 1;
    const TYPE_EMAIL_VERIFICATION = 2;
    const TYPE_CART_REMINDER = 3;
    const TYPE_ORDER_CREATED = 4;
    const TYPE_ORDER_COMPLETED = 5;
    const TYPE_ORDER_FAILED = 6;
    const TYPE_ORDER_CANCELED = 7;

    /**
     * The property defining the email types.
     *
     * @var array
     */
    public static $types = [
        self::TYPE_PASSWORD_RECOVERY => 'Password Recovery',
        self::TYPE_EMAIL_VERIFICATION => 'Email Verification',
        self::TYPE_CART_REMINDER => 'Cart Reminder',
        self::TYPE_ORDER_CREATED => 'Order Created',
        self::TYPE_ORDER_COMPLETED => 'Order Completed',
        self::TYPE_ORDER_FAILED => 'Order Failed',
        self::TYPE_ORDER_CANCELED => 'Order Canceled',
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
        self::TYPE_CART_REMINDER => [
            'class' => 'App\Mail\CartReminder',
            'view' => 'emails.cart_reminder',
            'preview_image' => 'cart_reminder.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'home_url',
                'cart_contents',
            ],
        ],
        self::TYPE_ORDER_CREATED => [
            'class' => 'App\Mail\OrderCreated',
            'view' => 'emails.order_created',
            'preview_image' => 'order_created.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'order_id',
                'order_status',
                'order_contents',
            ],
        ],
        self::TYPE_ORDER_COMPLETED => [
            'class' => 'App\Mail\OrderCompleted',
            'view' => 'emails.order_completed',
            'preview_image' => 'order_completed.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'order_id',
                'order_status',
                'order_contents',
            ],
        ],
        self::TYPE_ORDER_FAILED => [
            'class' => 'App\Mail\OrderFailed',
            'view' => 'emails.order_failed',
            'preview_image' => 'order_failed.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'order_id',
                'order_status',
                'order_contents',
            ],
        ],
        self::TYPE_ORDER_CANCELED => [
            'class' => 'App\Mail\OrderCanceled',
            'view' => 'emails.order_canceled',
            'preview_image' => 'order_canceled.jpg',
            'variables' => [
                'first_name',
                'last_name',
                'full_name',
                'order_id',
                'order_status',
                'order_contents',
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
        'home_url' => [
            'name' => 'Home URL',
            'label' => 'The home URL of the site.',
            'description' => 'This URL will direct the users to the site\'s homepage.',
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
        'order_id' => [
            'name' => 'Order ID',
            'label' => 'The identifier for an order.',
            'description' => 'This represents the order\'s identifier by which the order will be referenced anywhere.',
        ],
        'order_status' => [
            'name' => 'Order Status',
            'label' => 'The current status of an order.',
            'description' => 'This represents the order\'s status at the time the email was sent.',
        ],
        'order_contents' => [
            'name' => 'Order Contents',
            'label' => 'The contents of a user\'s shopping cart.',
            'description' => 'This automatically generates the HTML for a table displaying the products and their quantities from inside a user\'s shopping cart',
        ],
        'cart_contents' => [
            'name' => 'Cart Contents',
            'label' => 'The contents of a user\'s order.',
            'description' => 'This automatically generates the HTML for a table displaying the products and their quantities from inside the order',
        ],
    ];

    /**
     * Get the from address of an email instance.
     *
     * @return mixed
     */
    public function getFromAddressAttribute()
    {
        return $this->metadata->from_email ?? (
            setting()->value('company-email') ?: config('mail.from.address')
        );
    }

    /**
     * Get the from name of an email instance.
     *
     * @return mixed
     */
    public function getFromNameAttribute()
    {
        return $this->metadata->from_name ?? (
            setting()->value('company-email') ?: config('mail.from.address')
        );
    }

    /**
     * Get the reply to address of an email instance.
     *
     * @return mixed
     */
    public function getReplyToAttribute()
    {
        return $this->metadata->reply_to ?? (
            setting()->value('company-email') ?: config('mail.from.address')
        );
    }

    /**
     * Get the subject of an email instance.
     *
     * @return mixed
     */
    public function getAttachmentAttribute()
    {
        return $this->metadata->attachment ?? null;
    }

    /**
     * Get the subject of an email instance.
     *
     * @return mixed
     */
    public function getSubjectAttribute()
    {
        return $this->metadata->subject ?? null;
    }

    /**
     * Get the message of an email instance.
     *
     * @return mixed
     */
    public function getMessageAttribute()
    {
        return $this->metadata->message ?? null;
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
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('name', 'asc');
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
            throw EmailException::viewNotFound();
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
        return array_merge((array)$this->metadata ?? [], $data);
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
            throw EmailException::emailNotFound($identifier);
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
            ->limitRevisionsTo(100);
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
        return ActivityOptions::instance()
            ->logByField('name');
    }

    /**
     * Get the specific upload config parts for this model.
     *
     * @return array
     */
    public function getUploadConfig()
    {
        return [];
    }
}