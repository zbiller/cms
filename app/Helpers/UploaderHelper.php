<?php

namespace App\Helpers;

use Storage;
use Exception;
use App\Models\Model;
use App\Exceptions\UploadException;
use Illuminate\View\View;

class UploaderHelper
{
    /**
     * The view container instance.
     *
     * @var View
     */
    protected $view;

    /**
     * The field of the uploader input.
     * Generally this represents a field in the database that holds or can hold a path for an uploaded file.
     *
     * @var string
     */
    protected $field;

    /**
     * The label that comes along with the generated field.
     *
     * @var string
     */
    protected $label;

    /**
     * The loaded or unloaded model class on which the uploader manager will function.
     *
     * @var Model
     */
    protected $model;

    /**
     * The current uploaded file.
     *
     * @var UploadedHelper|null
     */
    protected $current;

    /**
     * The extensions accepted for an upload as array.
     *
     * @var array
     */
    protected $accept;

    /**
     * Flag indicating whether the upload manager should be disabled or not.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * The styles a file can have.
     * Mainly, this applies to images and videos only.
     * For any other file type, the original should suffice.
     *
     * @var array
     */
    protected $styles = [
        'original'
    ];

    /**
     * The type of files that can be uploaded.
     * These types will be separated in tabs on the uploader popup.
     * Accepted values: image | video | audio | file
     *
     * @var array
     */
    protected $types = [
        'image',
        'video',
        'audio',
        'file'
    ];

    /**
     * The default values for styles, types and accept.
     * This is used to re-initialize these properties after the uploader finished rendering.
     * This way, the next uploader instance on page, won't inherit values from the previous one.
     *
     * @var array
     */
    private $defaults = [
        'field' => null,
        'label' => null,
        'model' => null,
        'current' => null,
        'accept' => null,
        'disabled' => false,
        'styles' => [
            'original'
        ],
        'types' => [
            'image',
            'video',
            'audio',
            'file'
        ],
    ];

    /**
     * The index used to identify an uploader helper instance in a page with multiple uploads.
     *
     * @var int
     */
    private $index = 0;

    /**
     * @return View
     * @throws UploadException
     */
    public function manager()
    {
        //$this->index++;

        $this->checkModel()->checkField();
        $this->parseLabel()->parseTypes()->parseAccept();
        $this->generateCurrent()->generateStyles();
        $this->buildManagerView()->resetToDefaults();

        return $this->view;
    }

    /**
     * Set or get the name of an uploader instance.
     *
     * @param string|null $field
     * @return $this|string
     */
    public function field($field = null)
    {
        if ($field === null) {
            return $this->field;
        }

        $this->field = $field;

        return $this;
    }

    /**
     * Set or get the label for an uploader instance.
     *
     * @param string|null $label
     * @return $this|string
     */
    public function label($label = null)
    {
        if ($label === null) {
            return $this->label;
        }

        $this->label = $label;

        return $this;
    }

    /**
     * Set or get the model for an uploader instance.
     *
     * @param Model|null $model
     * @return $this|string
     */
    public function model(Model $model = null)
    {
        if ($model === null) {
            return $this->model;
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set or get the types for an uploader instance.
     *
     * @param array|string $types
     * @return $this|string
     */
    public function types(...$types)
    {
        if (!$types) {
            return $this->types;
        }

        $this->types = $types;

        return $this;
    }

    /**
     * Set or get the accepted extensions for an uploader instance.
     *
     * @param array|string $accept
     * @return $this|string
     */
    public function accept(...$accept)
    {
        if (!$accept) {
            return $this->accept;
        }

        $this->accept = $accept;

        return $this;
    }

    /**
     * Set the $disabled property to true.
     * This means that the current uploader instance will be disabled.
     * No upload or crop will be available, just viewing the existing uploaded file.
     *
     * @return $this
     */
    public function disabled()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * Build the helpers::uploader.manager view with the generated properties.
     *
     * @return $this
     */
    private function buildManagerView()
    {
        $this->view = view('helpers::uploader.manager')->with([
            'index' => rand(1, 999999),
            'field' => $this->field,
            'label' => $this->label,
            'model' => $this->model,
            'current' => $this->current,
            'upload' => $this->current ? $this->current->load() : null,
            'styles' => $this->styles,
            'types' => $this->types,
            'accept' => $this->accept,
            'disabled' => $this->disabled,
        ]);

        return $this;
    }

    /**
     * Reset the properties to their default value.
     * This way, the next uploader helper instance in the page won't inherit values from the previous one.
     *
     * @return $this
     */
    private function resetToDefaults()
    {
        $this->field = $this->defaults['field'];
        $this->label = $this->defaults['label'];
        $this->model = $this->defaults['model'];
        $this->current = $this->defaults['current'];
        $this->styles = $this->defaults['styles'];
        $this->types = $this->defaults['types'];
        $this->accept = $this->defaults['accept'];
        $this->disabled = $this->defaults['disabled'];

        return $this;
    }

    /**
     * Check if the uploader instance already has a current upload and set it.
     *
     * @return $this
     */
    private function generateCurrent()
    {
        if (!$this->model->exists) {
            return $this;
        }

        if (str_contains($this->field, 'metadata')) {
            try {
                if (Storage::disk(config('upload.storage.disk'))->exists($this->model->metadata($this->field))) {
                    $this->current = uploaded($this->model->metadata($this->field));
                }
            } catch (Exception $e) {
                $this->current = null;
            }
        } elseif ($this->model->{$this->field} && $this->model->{'_' . $this->field}->exists()) {
            $this->current = $this->model->{'_' . $this->field};
        }

        return $this;
    }

    /**
     * Set the styles of the current field referenced by the uploaded file.
     * If no styles are defined by the model, set the styles property to its default value.
     *
     * @return $this
     */
    private function generateStyles()
    {
        if (
            method_exists($this->model, 'getUploadConfig') &&
            ($styles = array_search_key_recursive($this->field, $this->model->getUploadConfig(), true))
        ) {
            $this->styles = array_keys($styles);
        } else {
            $this->styles = $this->defaults['styles'];
        }

        return $this;
    }

    /**
     * Create the label if it was not passed as an argument.
     *
     * @return $this
     */
    private function parseLabel()
    {
        if (!$this->label) {
            $this->label = title_case(str_replace('_', ' ', $this->field));
        }

        return $this;
    }

    /**
     * Clean the given types parameter if the developer passed wrong data.
     * Remove additional unwanted types.
     *
     * @return $this
     */
    private function parseTypes()
    {
        foreach ($this->types as $index => $type) {
            if (!in_array($type, ['image', 'video', 'audio', 'file'])) {
                unset($this->types[$index]);
            }
        }

        return $this;
    }

    /**
     * Clean the given accept parameter if the developer passed wrong data.
     * Refactor the accept parameter if it contains an "all" (*) attribute.
     *
     * @return $this
     */
    private function parseAccept()
    {
        if (count($this->accept) > 0 && in_array('*', $this->accept)) {
            $this->accept = null;
        }

        return $this;
    }

    /**
     * Check if a model instance was passed to the uploader helper.
     * The model must be of type App\Models\Model.
     *
     * @return $this
     * @throws UploadException
     */
    private function checkModel()
    {
        if (!$this->model) {
            throw new UploadException(
                'You must specify a loaded or unloaded instance of App\Models\Model for the uploader.' . PHP_EOL .
                'To do this, chain the model() method to the uploader() helper.'
            );
        }

        return $this;
    }

    /**
     * Check if field name was passed to the uploader helper.
     * The field must be a string representing an existing column on the model's database table.
     *
     * @return $this
     * @throws UploadException
     */
    private function checkField()
    {
        if (!$this->field) {
            throw new UploadException(
                'You must specify a field for the uploader.' . PHP_EOL .
                'To do this, chain the field() method to the uploader() helper.'
            );
        }

        return $this;
    }
}