<?php

namespace App\Traits;

use DB;
use Exception;
use ReflectionMethod;
use App\Models\Model;
use App\Models\Cms\Url;
use App\Options\UrlOptions;
use App\Options\SlugOptions;
use App\Exceptions\UrlException;
use Illuminate\Database\Eloquent\Builder;

trait HasUrl
{
    use HasSlug;

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\UrlOptions file.
     *
     * @var UrlOptions
     */
    protected static $urlOptions;

    /**
     * Flag to manually disable the url generation only for the current request.
     *
     * @var bool
     */
    protected static $shouldGenerateUrl = true;

    /**
     * Boot the trait.
     *
     * Check if the "getUrlOptions" method has been implemented on the underlying model class.
     * Eager load urls through anonymous global scope.
     * Trigger eloquent events to create, update, delete url.
     *
     * @return void
     */
    public static function bootHasUrl()
    {
        self::checkUrlOptions();

        self::$urlOptions = self::getUrlOptions();

        self::validateUrlOptions();

        static::addGlobalScope('url', function (Builder $builder) {
            $builder->with('url');
        });

        static::created(function (Model $model) {
            if (self::$shouldGenerateUrl === true) {
                $model->createUrl();
            }
        });

        static::updated(function (Model $model) {
            if (self::$shouldGenerateUrl === true) {
                $model->updateUrl();
            }
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->deleteUrl();
            }
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
     * Disable the url generation manually only for the current request.
     *
     * @return static
     */
    public function doNotGenerateUrl()
    {
        self::$shouldGenerateUrl = false;

        return $this;
    }

    /**
     * Get the options for the HasSlug trait.
     *
     * @return SlugOptions
     */
    public static function getSlugOptions()
    {
        return SlugOptions::instance()
            ->generateSlugFrom(self::$urlOptions->fromField)
            ->saveSlugTo(self::$urlOptions->toField);
    }

    /**
     * Create a new url for the model.
     *
     * @return void
     * @throws UrlException
     */
    public function createUrl()
    {
        if (!$this->getAttribute(self::$urlOptions->toField)) {
            return;
        }

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
    public function updateUrl()
    {
        if (!$this->getAttribute(self::$urlOptions->toField)) {
            return;
        }

        try {
            DB::transaction(function () {
                $this->url()->update([
                    'url' => $this->buildFullUrl()
                ]);

                if (self::$urlOptions->cascadeUpdate === true) {
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
    public function deleteUrl()
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
        $old = trim($this->getOriginal(self::$urlOptions->toField), '/');
        $new = trim($this->getAttribute(self::$urlOptions->toField), '/');

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
            (str_is('/', $prefix) ? '' : ($prefix ? $prefix . '/' : '')) .
            $this->getAttribute(self::$urlOptions->toField) .
            (str_is('/', $suffix) ? '' : ($suffix ? '/' . $suffix : ''));
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

        $segment = self::$urlOptions->{'url' . ucwords($type)};

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
     * Check if mandatory slug options have been properly set from the model.
     * Check if $fromField and $toField have been set.
     *
     * @return void
     * @throws UrlException
     */
    protected static function validateUrlOptions()
    {
        if (!self::$urlOptions->routeController || !self::$urlOptions->routeAction) {
            throw new UrlException(
                'The model ' . self::class . ' uses the HasUrl trait' . PHP_EOL .
                'You are required to set the routing from where Laravel will dispatch it\'s route requests.' . PHP_EOL .
                'You can do this from inside the getUrlOptions() method defined on the model.'
            );
        }

        if (!self::$urlOptions->fromField) {
            throw new UrlException(
                'The model ' . self::class . ' uses the HasUrl trait' . PHP_EOL .
                'You are required to set the field from where to generate the url slug ($fromField)' . PHP_EOL .
                'You can do this from inside the getUrlOptions() method defined on the model.'
            );
        }

        if (!self::$urlOptions->toField) {
            throw new UrlException(
                'The model ' . self::class . ' uses the HasUrl trait' . PHP_EOL .
                'You are required to set the field where to store the generated url slug ($toField)' . PHP_EOL .
                'You can do this from inside the getUrlOptions() method defined on the model.'
            );
        }
    }

    /**
     * Verify if the getUrlOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkUrlOptions()
    {
        if (!method_exists(self::class, 'getUrlOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getUrlOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getUrlOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getUrlOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}