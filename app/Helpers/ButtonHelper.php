<?php

namespace App\Helpers;

class ButtonHelper
{
    /**
     * Render a default link.
     *
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @param string|null $class
     * @param array $attributes
     * @return $this
     */
    public function action($text, $url, $icon = null, $class = null, $attributes = [])
    {
        return view('helpers::button.action')->with([
            'text' => $text,
            'url' => $url,
            'icon' => $icon,
            'class' => $class,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render a default submit button.
     *
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @param string|null $class
     * @param string|null $confirm
     * @param array $attributes
     * @return $this
     */
    public function submit($text, $url, $icon = null, $class = null, $confirm = null, $attributes = [])
    {
        return view('helpers::button.submit')->with([
            'text' => $text,
            'url' => $url,
            'icon' => $icon,
            'class' => $class,
            'confirm' => $confirm,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the add button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function add($url, array $attributes = [])
    {
        return view('helpers::button.add')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the edit button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function edit($url, array $attributes = [])
    {
        return view('helpers::button.edit')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the delete button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function delete($url, array $attributes = [])
    {
        return view('helpers::button.delete')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the cancel button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function cancel($url, array $attributes = [])
    {
        return view('helpers::button.cancel')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the update button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function update(array $attributes = [])
    {
        return view('helpers::button.update')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the filter button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function filter(array $attributes = [])
    {
        return view('helpers::button.filter')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the clear button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function clear(array $attributes = [])
    {
        return view('helpers::button.clear')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the view button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function view($url, array $attributes = [])
    {
        return view('helpers::button.view')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the download button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function download($url, array $attributes = [])
    {
        return view('helpers::button.download')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save button view helper.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function save(array $attributes = [])
    {
        return view('helpers::button.save')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the publish button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function publish($url, array $attributes = [])
    {
        return view('helpers::button.publish')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the rollback button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function rollback($url, array $attributes = [])
    {
        return view('helpers::button.rollback')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the duplicate button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function duplicate($url, array $attributes = [])
    {
        return view('helpers::button.duplicate')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the restore button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function restore($url, array $attributes = [])
    {
        return view('helpers::button.restore')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save elsewhere button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveElsewhere($url, array $attributes = [])
    {
        return view('helpers::button.save_elsewhere')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save new button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAsNew($url, array $attributes = [])
    {
        return view('helpers::button.save_new')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render save and stay button.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAndStay(array $attributes = [])
    {
        return view('helpers::button.save_stay')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the save as draft button.
     *
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAsDraft($url, array $attributes = [])
    {
        return view('helpers::button.save_draft')->with([
            'url' => $url,
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Build the attributes for a button (HTML style).
     *
     * @param array $attributes
     * @return array
     */
    protected static function buildAttributes(array $attributes = [])
    {
        $attr = [];

        foreach ($attributes as $key => $value) {
            $attr[] = $key . '="' . $value . '"';
        }

        return $attr;
    }
}