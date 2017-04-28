<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait CanOrder
{
    /**
     * Order entity rows based on the order and model specified from request (ajax).
     *
     * @param Request $request
     * @return void
     */
    public function order(Request $request)
    {
        $this->validate($request, [
            'model' => 'required',
            'items' => 'required|array'
        ]);

        app($request->get('model'))->setNewOrder(
            array_values($request->get('items'))
        );
    }
}
