<?php

namespace App\Helpers\View;

class Button
{
    /**
     * Render the add button view helper.
     *
     * @param string $route
     * @return \Illuminate\View\View
     */
    public function add($route)
    {
        return view('helpers::button.add')->with([
            'url' => route($route)
        ]);
    }

    /**
     * Render the edit button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @return \Illuminate\View\View
     */
    public function edit($route, array $parameters = [])
    {
        return view('helpers::button.edit')->with([
            'url' => route($route, $parameters)
        ]);
    }

    /**
     * Render the delete button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @return \Illuminate\View\View
     */
    public function delete($route, array $parameters = [])
    {
        return view('helpers::button.delete')->with([
            'url' => route($route, $parameters)
        ]);
    }

    /**
     * Render the cancel button view helper.
     *
     * @param string $route
     * @return \Illuminate\View\View
     */
    public function cancel($route)
    {
        return view('helpers::button.cancel')->with([
            'url' => route($route)
        ]);
    }

    /**
     * Render the update button view helper.
     *
     * @return \Illuminate\View\View
     */
    public function update()
    {
        return view('helpers::button.update');
    }

    /**
     * Render the filter button view helper.
     *
     * @return \Illuminate\View\View
     */
    public function filter()
    {
        return view('helpers::button.filter');
    }

    /**
     * Render the clear button view helper.
     *
     * @return \Illuminate\View\View
     */
    public function clear()
    {
        return view('helpers::button.clear');
    }

    /**
     * Render the clear button view helper.
     *
     * @param string $route
     * @param array $parameters
     * @return \Illuminate\View\View
     */
    public function download($route, array $parameters = [])
    {
        return view('helpers::button.download')->with([
            'url' => route($route, $parameters)
        ]);
    }

    /**
     * Render the save button view helper.
     *
     * @return \Illuminate\View\View
     */
    public function save()
    {
        return view('helpers::button.save');
    }

    /**
     * Render the save and stay button view helper.
     *
     * @return \Illuminate\View\View
     */
    public function saveStay()
    {
        return view('helpers::button.save_stay');
    }
}