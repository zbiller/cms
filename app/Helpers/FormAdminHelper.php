<?php

namespace App\Helpers;

use Collective\Html\FormFacade;
use Illuminate\Support\Collection;

class FormAdminHelper
{
    /**
     * The instance of the form facade.
     *
     * @var FormFacade
     */
    protected $form;

    /**
     * The current model instance for the form.
     *
     * @var mixed
     */
    protected $model;

    /**
     * @set form
     */
    public function __construct()
    {
        $this->form = FormFacade::getFacadeRoot();
    }

    /**
     * If an unknown method has been invoked, call the method on the Collective\Html\FormFacade.
     * If event the facade does not have that method, than it's __call() will be invoked.
     *
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     */
    public function __call($method, $arguments = null)
    {
        return call_user_func_array([$this->form, $method], $arguments);
    }

    /**
     * Wraps the input field into html to match the admin layout.
     *
     * @param string $input
     * @param string $label
     * @return string
     */
    public function wrap($input, $label)
    {
        return $label ? "<fieldset><label>{$label}</label>{$input}</fieldset>" : $input;
    }

    /**
     * Create a new model based form builder.
     *
     * @param mixed $model
     * @param array $options
     * @return string
     */
    public function model($model, array $options = [])
    {
        $this->model = $model;

        $this->form->setModel($model);

        return $this->open($options);
    }

    /**
     * Open up a new HTML form.
     *
     * @param array $options
     * @return string
     */
    public function open(array $options = [])
    {
        return $this->form->open($options);
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
        return $this->form->close();
    }

    /**
     * Create a submit button element.
     *
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function submit($value = null, array $options = [])
    {
        return $this->form->submit($value, $options);
    }

    /**
     * Create a reset button element.
     *
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function reset($value = null, array $options = [])
    {
        return $this->form->reset($value, $options);
    }

    /**
     * Create a button element.
     *
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function button($value = null, array $options = [])
    {
        return $this->form->button($value, $options);
    }

    /**
     * Create a hidden input field.
     *
     * @param string  $name
     * @param string  $value
     * @param array   $options
     * @return string
     */
    public function hidden($name, $value = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->form->hidden($this->name($name), $this->value($name, $value), $options);
    }

    /**
     * Create a text input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function text($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a textarea input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function textarea($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->textarea($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a select input field.
     *
     * @param string $name
     * @param string|null $label
     * @param array $list
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function select($name, $label = null, $list = [], $selected = null, array $options = [])
    {
        $list = $list instanceof Collection ? $list->toArray() : $list;
        $selected = $selected instanceof Collection ? $selected->toArray() : $selected;
        $options['class'] = 'select-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-selected'] = is_array($selected) ? json_encode($selected) : $selected;

        return $this->wrap(
            $this->form->select($this->name($name), $list, $this->value($name, $selected), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a password input field.
     *
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @param bool $generate
     * @return string
     */
    public function password($name, $label = null, array $options = [], $generate = false)
    {
        if ($generate) {
            $options['class'] = 'with-generate-button ' . (isset($options['class']) ? $options['class'] : '');
        }

        return $this->wrap($this->form->password($this->name($name), $options) . (
            $generate ? '<a href="#" id="password-generate" class="btn red"><i class="fa fa-random"></i>&nbsp; Generate</a>' : ''
        ), $this->label($name, $label));
    }

    /**
     * Create a file input field.
     *
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function file($name, $label = null, array $options = [])
    {
        return $this->wrap(
            '<label class="file-input">' . $this->form->file($this->name($name), $options) . '<span>No file chosen</span></label>',
            $this->label($name, $label)
        );
    }

    /**
     * Create a number input field.
     *
     * @param string $name
     * @param string|null $value
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function number($name, $value = null, $label = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->number($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create an email input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function email($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->email($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a phone input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function phone($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->tel($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a checkbox input field.
     *
     * @param string $name
     * @param string|null $label
     * @param int|null|string $value
     * @param bool|null $checked
     * @param array $options
     * @return string
     */
    public function checkbox($name, $label = null, $value = 1, $checked = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->checkbox($this->name($name), $this->value($name, $value), $checked, $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a radio input field.
     *
     * @param string $name
     * @param string|null $label
     * @param int|null|string $value
     * @param bool|null $checked
     * @param array $options
     * @return string
     */
    public function radio($name, $label = null, $value = 1, $checked = null, array $options = [])
    {
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->radio($this->name($name), $this->value($name, $value), $checked, $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create an editor field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function editor($name, $label = null, $value = null, array $options = [])
    {
        $options['class'] = 'editor-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->textarea($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a calendar input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function calendar($name, $label = null, $value = null, array $options = [])
    {
        $options['class'] = 'date-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a time input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function time($name, $label = null, $value = null, array $options = [])
    {
        $options['class'] = 'time-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a color input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function color($name, $label = null, $value = null, array $options = [])
    {
        $options['class'] = 'color-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a select input field for a range.
     *
     * @param string $name
     * @param string|null $label
     * @param int $start
     * @param int $end
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function selectRange($name, $label = null, $start = 0, $end = 0, $selected = null, array $options = [])
    {
        $options['class'] = 'select-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-selected'] = $selected;

        return $this->wrap(
            $this->form->selectRange($this->name($name), $start, $end, $this->value($name, $selected), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a select input field for years.
     *
     * @param string $name
     * @param string|null $label
     * @param int $start
     * @param int $end
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function selectYear($name, $label = null, $start = null, $end = null, $selected = null, array $options = [])
    {
        $options['class'] = 'select-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-selected'] = $selected;

        return $this->wrap(
            $this->form->selectYear($this->name($name), $start ?: 1970, $end ?: date('Y'), $this->value($name, $selected), $options),
            $this->label($name, $label)
        );
    }

    /**
     * Create a select input field for months.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $selected
     * @param array $options
     * @param string $format
     * @return string
     */
    public function selectMonth($name, $label = null, $selected = null, array $options = [], $format = '%B')
    {
        $options['class'] = 'select-input ' . (isset($options['class']) ? $options['class'] : '');
        $options['data-selected'] = $selected;

        return $this->wrap(
            $this->form->selectMonth($this->name($name), $this->value($name, $selected), $options, $format),
            $this->label($name, $label)
        );
    }

    /**
     * Set the name of the field.
     *
     * @param string $name
     * @return mixed
     */
    protected function name($name)
    {
        return $name;
    }

    /**
     * Set the value of the field.
     *
     * @param string $name
     * @param string $value
     * @return mixed
     */
    protected function value($name, $value = null)
    {
        return $value;
    }

    /**
     * Set the label using the name if no label was specified.
     * Specify the label to false to render only the input, without any wrappings.
     *
     * @param string $name
     * @param null $label
     * @return string
     */
    protected function label($name, $label = null)
    {
        if ($label === false) {
            return false;
        }

        return $label ?: ucfirst(preg_replace("/[^a-zA-Z0-9\s]/", " ", $name));
    }
}