<?php

namespace App\Helpers\Message;

class Flash
{
    /**
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
            case session()->has('flash_alert');
                return $this->alert();
                break;
        }

        return null;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function success()
    {
        return $this->show('success', session()->get('flash_success'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function error()
    {
        return $this->show('error', session()->get('flash_error'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function alert()
    {
        return $this->show('alert', session()->get('flash_alert'));
    }

    /**
     * @param string $type
     * @param string $message
     * @return \Illuminate\View\View
     */
    protected function show($type, $message)
    {
        return view('helpers::flash.message')->with([
            'type' => $type,
            'message' => $message
        ]);
    }
}