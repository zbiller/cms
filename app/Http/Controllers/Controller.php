<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Boot the child controller.
     * Boot the child controller's traits.
     *
     * @set boot
     */
    public function __construct()
    {
        $this->bootController();
        $this->bootTraits();
    }

    /**
     * @return void
     */
    private function bootController()
    {
        if (method_exists($this, 'boot')) {
            $arguments = [];

            foreach ((new \ReflectionMethod($this, 'boot'))->getParameters() as $parameter) {
                preg_match("/.*<required> (.+) \\\${$parameter->getName()}/", $parameter, $hint);

                if (count($hint) == 2) {
                    array_push($arguments, $class = app($hint[1]));
                }
            }

            call_user_func_array([&$this, 'boot'], $arguments);
        }
    }

    /**
     * @return void
     */
    private function bootTraits()
    {
        foreach (class_uses_recursive(static::class) as $trait) {
            if (method_exists(static::class, $method = 'boot' . class_basename($trait))) {
                forward_static_call([static::class, $method]);
            }
        }
    }
}
