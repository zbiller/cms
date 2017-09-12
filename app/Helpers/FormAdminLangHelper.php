<?php

namespace App\Helpers;

use Collective\Html\FormFacade;

class FormAdminLangHelper extends FormAdminHelper
{
    /**
     * Create a new model based form builder.
     *
     * @param mixed $model
     * @param array $options
     * @return string
     */
    public function model($model, array $options = array())
    {
        $this->model = $model;

        return parent::model($model, $options);
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function name($name)
    {
        return $name . '[' . app()->getLocale() . ']';
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return mixed|null
     */
    protected function value($name, $value = null)
    {
        if ($value) {
            return $value;
        }

        return $this->model ? $this->model->getTranslation($name, app()->getLocale(), false) : null;
    }
}