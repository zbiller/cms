<?php

namespace App\Helpers;

class FlashHelper
{
    /**
     * The flash type to be rendered.
     * For now, only "default" and "admin" are available.
     * The message() method on this helper will try to display the view with the name of this property.
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
     * Render any flash message if it's set.
     *
     * @return string|null
     */
    public function message()
    {
        switch (session()) {
            case session()->has('flash_success');
                return $this->success();
                break;
            case session()->has('flash_error');
                return $this->error();
                break;
            case session()->has('flash_warning');
                return $this->warning();
                break;
        }

        return $this->show(null, null);
    }

    /**
     * Render the success flash message.
     *
     * @return \Illuminate\View\View
     */
    public function success()
    {
        return $this->show('success', session()->get('flash_success'));
    }

    /**
     * Render the error flash message.
     *
     * @return \Illuminate\View\View
     */
    public function error()
    {
        return $this->show('error', session()->get('flash_error'));
    }

    /**
     * Render the warning flash message.
     *
     * @return \Illuminate\View\View
     */
    public function warning()
    {
        return $this->show('warning', session()->get('flash_warning'));
    }

    /**
     * Render the actual view helper that displays flash messages.
     *
     * @param string $type
     * @param string $message
     * @return \Illuminate\View\View
     */
    protected function show($type, $message)
    {
        return view("helpers::flash.message.{$this->type}")->with([
            'type' => $type,
            'message' => $message
        ]);
    }
}