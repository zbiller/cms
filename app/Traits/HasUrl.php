<?php

namespace App\Traits;

use DB;
use Exception;
use App\Models\Model;
use App\Models\Cms\Url;
use App\Options\HasUrlOptions;
use App\Options\HasSlugOptions;
use App\Exceptions\UrlException;

trait HasUrl
{
    use HasSlug;

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\HasSlugOptions file.
     *
     * @var HasSlugOptions
     */
    protected $hasUrlOptions;

    /**
     * The method used for setting the slug options.
     * This method should be called inside the model using this trait.
     * Inside the method, you should set all the slug options.
     * This can be achieved using the methods from App\Options\HasUrlOptions.
     *
     * @return HasUrlOptions
     */
    abstract function getHasUrlOptions(): HasUrlOptions;

    /**
     * Boot the trait.
     *
     * Init UrlOptions on "creating" and "updating" so the HasSlug trait will know with what data to work.
     *
     * @return void
     */
    public static function bootHasUrl()
    {
        static::creating(function (Model $model) {
            $model->initUrlOptions();
        });

        static::updating(function (Model $model) {
            $model->initUrlOptions();
        });

        static::created(function (Model $model) {
            $model->createUrl();
        });

        static::updated(function (Model $model) {
            $model->updateUrl();
        });

        static::deleted(function (Model $model) {
            $model->deleteUrl();
        });
    }

    /**
     * Get the model's url.
     *
     * @return mixed
     */
    public function url()
    {
        return $this->morphOne(Url::class, 'urlable');
    }

    /**
     * Get the options for the HasSlug trait.
     *
     * @return HasSlugOptions
     */
    public function getHasSlugOptions()
    {
        return HasSlugOptions::instance()
            ->generateSlugFrom($this->hasUrlOptions->fromField)
            ->saveSlugTo($this->hasUrlOptions->toField);
    }

    /**
     * Create a new url for the model.
     *
     * @return void
     * @throws UrlException
     */
    protected function createUrl()
    {
        try {
            $this->url()->create([
                'url' => $this->buildFullUrl()
            ]);
        } catch (Exception $e) {
            throw new UrlException(
                'Could not create the URL!', $e->getCode(), $e
            );
        }
    }

    /**
     * Update the existing url for the model.
     *
     * @return void
     * @throws UrlException
     */
    protected function updateUrl()
    {
        try {
            DB::transaction(function () {
                $this->url()->update([
                    'url' => $this->buildFullUrl()
                ]);

                if ($this->hasUrlOptions->cascadeUpdate === true) {
                    $this->updateUrlsInCascade();
                }
            });
        } catch (Exception $e) {
            throw new UrlException(
                'Could not update the URL!', $e->getCode(), $e
            );
        }
    }

    /**
     * Delete the url for the just deleted model.
     *
     * @return void
     * @throws UrlException
     */
    protected function deleteUrl()
    {
        try {
            $this->url()->delete();
        } catch (Exception $e) {
            throw new UrlException('Could not delete the URL!', $e->getCode(), $e);
        }
    }

    /**
     * Synchronize children urls for the actual model's url.
     * Saves all children urls of the model in use with the new parent model's slug.
     *
     * @return void
     */
    protected function updateUrlsInCascade()
    {
        $old = trim($this->getOriginal($this->hasUrlOptions->toField), '/');
        $new = trim($this->{$this->hasUrlOptions->toField}, '/');

        $children = URL::where('urlable_type', static::class)->where(function ($query) use ($old) {
            $query->where('url', 'like', "{$old}/%")->orWhere('url', 'like', "%/{$old}/%");
        })->get();

        foreach ($children as $child) {
            $child->update([
                'url' => str_replace($old . '/', $new . '/', $child->url)
            ]);
        }
    }

    /**
     * Get the full relative url.
     * The full url will also include the prefix and suffix if any was provided.
     *
     * @return string
     */
    protected function buildFullUrl()
    {
        $prefix = $this->buildUrlSegment('prefix');
        $suffix = $this->buildUrlSegment('suffix');

        return
            ($prefix ? $prefix . '/' : '') .
            $this->{$this->hasUrlOptions->toField} .
            ($suffix ? '/' . $suffix : '');
    }

    /**
     * Build the url segment.
     * This can be either "prefix" or "suffix".
     * The accepted parameter $type accepts only "prefix" and "suffix" as it's value.
     * Otherwise, the method will return an empty string.
     *
     * @param string $type
     * @return mixed|string
     */
    protected function buildUrlSegment($type)
    {
        if ($type != 'prefix' && $type != 'suffix') {
            return '';
        }

        $segment = $this->hasUrlOptions->{'url' . ucwords($type)};

        if (is_callable($segment)) {
            return call_user_func_array($segment, ['', $this]);
        } elseif (is_array($segment)) {
            return implode('/', $segment);
        } elseif (is_string($segment)) {
            return $segment;
        } else {
            return '';
        }
    }

    /**
     * Set the url options.
     *
     * @return $this
     */
    protected function initUrlOptions()
    {
        $this->hasUrlOptions = $this->getHasUrlOptions();

        return $this;
    }

    /**
     * Check if mandatory slug options have been properly set from the model.
     * Check if $fromField and $toField have been set.
     *
     * @return void
     * @throws UrlException
     */
    protected function checkUrlOptions()
    {
        if (!count($this->hasUrlOptions->fromField)) {
            throw new UrlException(
                'The model ' . get_class($this) . ' uses the HasUrl trait' . PHP_EOL .
                'You are required to set the field from where to generate the url slug ($fromField)' . PHP_EOL .
                'You can do this from inside the getHasUrlOptions() method defined on the model.'
            );
        }

        if (!strlen($this->hasUrlOptions->toField)) {
            throw new UrlException(
                'The model ' . get_class($this) . ' uses the HasUrl trait' . PHP_EOL .
                'You are required to set the field where to store the generated url slug ($toField)' . PHP_EOL .
                'You can do this from inside the getHasUrlOptions() method defined on the model.'
            );
        }
    }
}