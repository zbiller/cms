<?php

namespace App\Helpers;

class ButtonHelper
{
    /**
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
     * Render the add button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function add($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.add')->with([
            'url' => route($route, $parameters),
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the edit button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function edit($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.edit')->with([
            'url' => route($route, $parameters),
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the delete button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function delete($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.delete')->with([
            'url' => route($route, $parameters),
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the cancel button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function cancel($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.cancel')->with([
            'url' => route($route, $parameters),
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the update button view helper.
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
     * Render the filter button view helper.
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
     * Render the clear button view helper.
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
     * Render the clear button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function view($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.view')->with([
            'url' => route($route, $parameters),
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the clear button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function download($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.download')->with([
            'url' => route($route, $parameters),
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
     * Render the restore button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function restore($route, array $parameters = [], array $attributes = [])
    {
        return view('helpers::button.restore')->with([
            'url' => route($route, $parameters),
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
     * Render the save and stay button view helper.
     *
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveStay(array $attributes = [])
    {
        return view('helpers::button.save_stay')->with([
            'attributes' => self::buildAttributes($attributes)
        ]);
    }

    /**
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