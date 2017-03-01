<?php

namespace App\Helpers;

class Button
{
    /**
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
     * @return \Illuminate\View\View
     */
    public function update()
    {
        return view('helpers::button.update');
    }
}