<?php

namespace App\Helpers;

class ValidationHelper
{
    /**
     * The validation type to be rendered.
     * For now, only "default" and "admin" are available.
     * The errors() method on this helper will try to display the view containing the name of this property.
     *
     * @var string
     */
    protected $type = 'default';

    /**
     * Set the pagination type (view) to render.
     *
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        if ($type) {
            $this->type = $type;
        }
    }

    /**
     * Display the validation errors for a request.
     *
     * @return \Illuminate\View\View
     */
    public function errors()
    {
        return view("helpers::validation.errors.{$this->type}");
    }
}