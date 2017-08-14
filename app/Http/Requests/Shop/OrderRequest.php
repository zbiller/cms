<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use App\Models\Shop\Order;
use Illuminate\Validation\Rule;

class OrderRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $model = null;

        if ($this->route('order')) {
            $model = $this->route('order');
        } elseif ($this->route('id')) {
            $model = Order::withDrafts()->withTrashed()->find($this->route('id'));
        }

        return [
            'identifier' => [
                'required',
                Rule::unique('orders', 'identifier')
                    ->ignore($model && $model->exists ? $model->id : null),
            ],
            'raw_total' => [
                'nullable',
                'numeric',
            ],
            'sub_total' => [
                'nullable',
                'numeric',
            ],
            'grand_total' => [
                'nullable',
                'numeric',
            ],
            'customer.first_name' => [
                'required',
            ],
            'customer.last_name' => [
                'required',
            ],
            'customer.email' => [
                'required',
                'email',
            ],
            'addresses.shipping.city' => [
                'required',
            ],
            'addresses.shipping.address' => [
                'required',
            ],
            'addresses.delivery.city' => [
                'required',
            ],
            'addresses.delivery.address' => [
                'required',
            ],
            'payment' => [
                'required',
                Rule::in(array_keys(Order::$payments)),
            ],
            'shipping' => [
                'required',
                Rule::in(array_keys(Order::$shippings)),
            ],
            'status' => [
                'required',
                Rule::in(array_keys(Order::$statuses)),
            ],
            'viewed' => [
                'nullable',
                Rule::in(array_keys(Order::$views)),
            ],
        ];
    }

    /**
     * Get the pretty name of attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'raw_total' => 'raw total',
            'sub_total' => 'sub total',
            'grand_total' => 'grand total',
            'customer.first_name' => 'first name',
            'customer.last_name' => 'last name',
            'customer.email' => 'email',
            'addresses.shipping.city' => 'shipping city',
            'addresses.shipping.address' => 'shipping address',
            'addresses.delivery.city' => 'delivery city',
            'addresses.delivery.address' => 'delivery address',
        ];
    }
}
