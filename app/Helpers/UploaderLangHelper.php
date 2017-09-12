<?php

namespace App\Helpers;

class UploaderLangHelper extends UploaderHelper
{
    /**
     * Set or get the name of an uploader instance.
     *
     * @param string|null $field
     * @return $this|string
     */
    public function field($field = null)
    {
        if ($field === null) {
            return str_replace('[' . app()->getLocale() . ']', '', $this->field);
        }

        $this->field = $field . '[' . app()->getLocale() . ']';

        return $this;
    }

    /**
     * Check if the uploader instance already has a current upload and set it.
     *
     * @return $this
     */
    protected function generateCurrent()
    {
        if (!$this->model->exists) {
            return $this;
        }

        $upload = uploaded($this->model->getTranslation(
            $this->field(), app()->getLocale(), false
        ));

        $this->current = $upload->exists() ? $upload : null;

        return $this;
    }
}