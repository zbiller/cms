<?php

namespace App\Models\Localisation;

use App\Exceptions\LanguageException;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class Language extends Model
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'default',
        'active',
    ];

    /**
     * The constants defining the languages default.
     *
     * @const
     */
    const DEFAULT_NO = 0;
    const DEFAULT_YES = 1;

    /**
     * The constants defining the languages availability.
     *
     * @const
     */
    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;

    /**
     * The property defining the languages default.
     *
     * @var array
     */
    public static $defaults = [
        self::DEFAULT_NO => 'No',
        self::DEFAULT_YES => 'Yes',
    ];

    /**
     * The property defining the languages availability.
     *
     * @var array
     */
    public static $actives = [
        self::ACTIVE_NO => 'No',
        self::ACTIVE_YES => 'Yes',
    ];

    /**
     * Boot the model.
     *
     * When a language is set as default, set all other languages as non-default.
     *
     * When deleting a language, check if the language is the default one.
     * If it is, send an error that will be parsed in the controller for the user.
     *
     * @return void
     */
    public static function boot()
    {
        static::saving(function ($model) {
            if ($model->getOriginal('default') == self::DEFAULT_YES && $model->getAttribute('default') == self::DEFAULT_NO) {
                throw LanguageException::oneDefaultIsRequired();
            }

            if ($model->isDirty('default') && $model->getAttribute('default') == self::DEFAULT_YES) {
                try {
                    static::where('id', '!=', $model->id)->update([
                        'default' => self::DEFAULT_NO
                    ]);
                } catch (Exception $e) {
                    throw LanguageException::replacingTheDefaultHasFailed();
                }
            }

            return true;
        });

        static::deleting(function ($model) {
            if ($model->getAttribute('default') == self::DEFAULT_YES) {
                throw LanguageException::deletingTheDefaultIsRestricted();
            }

            return true;
        });
    }

    /**
     * Filter the query to show only default results.
     *
     * @param Builder $query
     */
    public function scopeOnlyDefault($query)
    {
        $query->where('default', self::DEFAULT_YES);
    }

    /**
     * Filter the query to show only non-default results.
     *
     * @param Builder $query
     */
    public function scopeExcludingDefault($query)
    {
        $query->where('default', self::DEFAULT_NO);
    }

    /**
     * Filter the query to return only active results.
     *
     * @param Builder $query
     */
    public function scopeOnlyActive($query)
    {
        $query->where('active', self::ACTIVE_YES);
    }

    /**
     * Filter the query to return only inactive results.
     *
     * @param Builder $query
     */
    public function scopeOnlyInactive($query)
    {
        $query->where('active', self::ACTIVE_NO);
    }

    /**
     * Filter the query by code.
     *
     * @param Builder $query
     * @param string $code
     */
    public function scopeWhereCode($query, $code)
    {
        $query->where('code', $code);
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
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('name');
    }
}