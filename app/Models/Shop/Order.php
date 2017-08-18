<?php

namespace App\Models\Shop;

use App\Events\OrderCanceled;
use App\Events\OrderCompleted;
use App\Events\OrderCreated;
use App\Events\OrderFailed;
use App\Exceptions\OrderException;
use App\Models\Auth\User;
use App\Models\Model;
use App\Models\Shop\Order\Item;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use SoftDeletes;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier',
        'currency',
        'customer',
        'addresses',
        'payment',
        'shipping',
        'status',
    ];

    /**
     * The constants defining the order statuses.
     *
     * @const
     */
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELED = 3;
    const STATUS_FAILED = 4;

    /**
     * The constants defining the order's viewing options.
     *
     * @const
     */
    const VIEWED_NO = 0;
    const VIEWED_YES = 1;

    /**
     * The constants defining the payment methods.
     *
     * @const
     */
    const PAYMENT_CASH_DELIVERY = 1;
    const PAYMENT_CREDIT_CARD = 2;

    /**
     * The constants defining the shipping methods.
     *
     * @const
     */
    const SHIPPING_PERSONAL_LIFTING = 1;
    const SHIPPING_NORMAL_COURIER = 2;
    const SHIPPING_EXPRESS_COURIER = 3;

    /**
     * The property defining the order statuses.
     *
     * @var array
     */
    public static $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_FAILED => 'Failed',
    ];

    /**
     * The property defining the order's viewing options.
     *
     * @var array
     */
    public static $views = [
        self::VIEWED_NO => 'No',
        self::VIEWED_YES => 'Yes',
    ];

    /**
     * The property defining the payment methods.
     *
     * @var array
     */
    public static $payments = [
        self::PAYMENT_CASH_DELIVERY => 'Cash on delivery',
        self::PAYMENT_CREDIT_CARD => 'Credit card',
    ];

    /**
     * The property defining the shipping methods.
     *
     * @var array
     */
    public static $shippings = [
        self::SHIPPING_PERSONAL_LIFTING => 'Personal lifting',
        self::SHIPPING_NORMAL_COURIER => 'Normal Courier',
        self::SHIPPING_EXPRESS_COURIER => 'Express Courier',
    ];

    /**
     * The array containing the general order details:
     * Should only contain these keys and their associated values:
     *
     * - identifier (optional)
     * - currency (optional)
     * - payment (optional)
     * - shipping (optional)
     * - status (optional)
     *
     * @var array
     */
    protected static $data = [
        'identifier' => null,
        'currency' => null,
        'payment' => null,
        'shipping' => null,
        'status' => null,
    ];

    /**
     * The array containing the customer's details:
     * Should only contain these keys and their associated values:
     *
     * - first_name (required)
     * - last_name (required)
     * - email (required)
     * - phone (required)
     *
     * @var array
     */
    protected static $customer = [
        'first_name' => null,
        'last_name' => null,
        'email' => null,
        'phone' => null,
    ];

    /**
     * The array containing the order's shipping and delivery addresses:
     * Should only contain these keys and their associated values:
     *
     * - shipping (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     *
     * - delivery (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     *
     * @var array
     */
    protected static $addresses = [
        'shipping' => [
            'country' => null,
            'state' => null,
            'city' => null,
            'address' => null,
        ],
        'delivery' => [
            'country' => null,
            'state' => null,
            'city' => null,
            'address' => null,
        ],
    ];

    /**
     * The array containing the order's items:
     * Should only contain these keys and their associated values:
     *
     * - 0 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - 1 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - etc.
     *
     * @var array
     */
    protected static $items = [];

    /**
     * An order has many items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'order_id');
    }

    /**
     * Set the json encoded data for the "customer" column.
     *
     * @param $value
     */
    public function setCustomerAttribute($value)
    {
        $this->attributes['customer'] = $value ? (
            is_json_format($value) ? $value : json_encode($value)
        ) : null;
    }

    /**
     * Set the json encoded data for the "addresses" column.
     *
     * @param $value
     */
    public function setAddressesAttribute($value)
    {
        $this->attributes['addresses'] = $value ? (
            is_json_format($value) ? $value : json_encode($value)
        ) : null;
    }

    /**
     * Get the json decoded representation of the "customer" column.
     *
     * @return mixed
     */
    public function getCustomerAttribute()
    {
        return json_decode($this->attributes['customer']);
    }

    /**
     * Get the json decoded representation of the "addresses" column.
     *
     * @return mixed
     */
    public function getAddressesAttribute()
    {
        return json_decode($this->attributes['addresses']);
    }

    /**
     * Get the first name of the customer.
     *
     * @return string|null
     */
    public function getFirstNameAttribute()
    {
        return isset($this->customer->first_name) ? $this->customer->first_name : null;
    }

    /**
     * Get the last name of the customer.
     *
     * @return string|null
     */
    public function getLastNameAttribute()
    {
        return isset($this->customer->last_name) ? $this->customer->last_name : null;
    }

    /**
     * Get the full name of the customer.
     *
     * @return string|null
     */
    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    /**
     * Get the email address of the customer.
     *
     * @return string|null
     */
    public function getEmailAttribute()
    {
        return isset($this->customer->email) ? $this->customer->email : null;
    }

    /**
     * Get the phone number of the customer.
     *
     * @return string|null
     */
    public function getPhoneAttribute()
    {
        return isset($this->customer->phone) ? $this->customer->phone : null;
    }

    /**
     * Get the shipping address details.
     *
     * @return string|null
     */
    public function getShippingAddressAttribute()
    {
        return isset($this->addresses->shipping) ? $this->addresses->shipping : null;
    }

    /**
     * Get the delivery address details.
     *
     * @return string|null
     */
    public function getDeliveryAddressAttribute()
    {
        return isset($this->addresses->delivery) ? $this->addresses->delivery : null;
    }

    /**
     * Create a new order.
     *
     * The $data parameter should be an array containing:
     * - identifier (optional)
     * - currency (optional)
     * - payment (optional)
     * - shipping (optional)
     * - status (optional)
     *
     * The $customer parameter should be an array containing:
     * - first_name (required)
     * - last_name (required)
     * - email (required)
     * - phone (required)
     *
     * The $addresses parameter should be an array containing:
     * - shipping (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     * - delivery (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     *
     * The $items parameter should be an array containing:
     * - 0 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - 1 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - etc.
     *
     * @param array $data
     * @param array $customer
     * @param array $addresses
     * @param array $items
     * @return Order
     * @throws OrderException
     */
    public static function createOrder(array $data, array $customer, array $addresses, array $items)
    {
        static::setOrderData($data);
        static::setOrderCustomer($customer);
        static::setOrderAddresses($addresses);
        static::setOrderItems($items);

        try {
            $order = DB::transaction(function () {
                $data = static::getFullOrderData();
                $order = Order::create($data);

                static::syncOrderItems($order);
                static::syncOrderTotals($order);

                event(new OrderCreated($order));

                return $order;
            });

            return $order;
        } catch (Exception $e) {
            throw new OrderException(
                'Could not create the order!', $e->getCode(), $e
            );
        }
    }

    /**
     * Update an existing order.
     *
     * The $data parameter should be an array containing:
     * - identifier (optional)
     * - currency (optional)
     * - payment (optional)
     * - shipping (optional)
     * - status (optional)
     * Include only the keys that are modified and need updating.
     *
     * The $customer parameter should be an array containing:
     * - first_name (optional)
     * - last_name (optional)
     * - email (optional)
     * - phone (optional)
     * Include only the keys that are modified and need updating.
     *
     * The $addresses parameter should be an array containing:
     * - shipping (optional)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (optional)
     * ----- address (optional)
     * - delivery (optional)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (optional)
     * ----- address (optional)
     * Include only the keys that are modified and need updating.
     *
     * The $items parameter should be an array containing:
     * - 0 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - 1 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - etc.
     *
     * @param int $id
     * @param array $data
     * @param array $customer
     * @param array $addresses
     * @param array $items
     * @return Order
     * @throws OrderException
     */
    public static function updateOrder($id, array $data, array $customer, array $addresses, array $items)
    {
        try {
            $order = static::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw $e;
        }

        static::setOrderData($data, $order);
        static::setOrderCustomer($customer, $order);
        static::setOrderAddresses($addresses, $order);
        static::setOrderItems($items, $order);

        try {
            $order = DB::transaction(function () use ($order) {
                $data = static::getFullOrderData();
                $original = $order->getOriginal();

                $order->update($data);

                static::syncOrderItems($order);
                static::syncOrderTotals($order);

                if (isset($data['status']) && (int)$data['status'] != $original['status']) {
                    switch ((int)$data['status']) {
                        case self::STATUS_COMPLETED:
                            event(new OrderCompleted($order));
                            break;
                        case self::STATUS_FAILED:
                            event(new OrderFailed($order));
                            break;
                        case self::STATUS_CANCELED:
                            event(new OrderCanceled($order));
                            break;
                    }
                }

                return $order;
            });

            return $order;
        } catch (Exception $e) {
            dd($e);
            throw new OrderException(
                'Could not update the order!', $e->getCode(), $e
            );
        }
    }

    /**
     * Sync the order totals based on the order items present.
     * Dynamically update the totals without letting the user manipulate them.
     *
     * @param Order $order
     * @return void
     * @throws OrderException
     */
    protected static function syncOrderTotals(Order $order)
    {
        try {
            $order->raw_total = 0;
            $order->sub_total = 0;
            $order->grand_total = 0;

            foreach ($order->items as $item) {
                $order->raw_total += Currency::convert(
                    $item->raw_total, $item->currency, $order->currency
                );

                $order->sub_total += Currency::convert(
                    $item->sub_total, $item->currency, $order->currency
                );

                $order->grand_total += Currency::convert(
                    $item->grand_total, $item->currency, $order->currency
                );
            }

            $discount = static::getDiscountValue($order->grand_total);
            $tax = static::getTaxValue($order->grand_total);

            $order->sub_total = $order->sub_total - $discount;
            $order->grand_total = $order->grand_total - $discount + $tax;

            $order->save();
        } catch (Exception $e) {
            throw new OrderException(
                'Could not sync the order\'s totals.', $e->getCode(), $e
            );
        }
    }

    /**
     * Sync the order items for an order.
     * Dynamically update all items details without letting the user manipulate the totals.
     *
     * @param Order $order
     * @return void
     * @throws OrderException
     */
    protected static function syncOrderItems(Order $order)
    {
        $order->items()->delete();

        foreach (self::$items as $item) {
            if (!isset($item['product_id'])) {
                throw OrderException::invalidOrderItem();
            }

            try {
                $product = Product::findOrFail($item['product_id']);
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                $item = new Item;

                $item->order_id = $order->id;
                $item->product_id = $product->id;
                $item->name = $product->name;
                $item->currency = $product->currency->code;
                $item->quantity = $quantity;
                $item->raw_price = $product->price;
                $item->sub_price = $product->price_with_discounts;
                $item->grand_price = $product->final_price;
                $item->raw_total = $product->price * $quantity;
                $item->sub_total = $product->price_with_discounts * $quantity;
                $item->grand_total = $product->final_price * $quantity;

                $item->save();
            } catch (ModelNotFoundException $e) {
                throw OrderException::invalidOrderItemProduct();
            } catch (Exception $e) {
                throw new OrderException(
                    'Could not sync the order item.', $e->getCode(), $e
                );
            }
        }
    }

    /**
     * Concatenate the $data, $customer and $addresses.
     * Get a full array of data compatible with the "create" method.
     *
     * @return array
     */
    protected static function getFullOrderData()
    {
        return self::$data + ['customer' => self::$customer] + ['addresses' => self::$addresses];
    }

    /**
     * Get the order's discount value based on discounts applicable on order only.
     *
     * @param float $total
     * @return float|int|null
     */
    protected static function getDiscountValue($total)
    {
        $price = $total;

        foreach (Discount::forOrder()->active()->get() as $discount) {
            if (!$discount->canBeApplied($total)) {
                continue;
            }

            switch ($discount->type) {
                case Discount::TYPE_FIXED:
                    $price -= $discount->rate;
                    break;
                case Discount::TYPE_PERCENT:
                    $price -= ($discount->rate / 100) * $price;
                    break;
            }
        }

        return $total - $price;
    }

    /**
     * Get the order's tax value based on taxes applicable on order only.
     *
     * @param $total
     * @return float|int|null
     */
    protected static function getTaxValue($total)
    {
        $price = $total;

        foreach (Tax::forOrder()->active()->get() as $tax) {
            if (!$tax->canBeApplied($total)) {
                continue;
            }

            switch ($tax->type) {
                case Tax::TYPE_FIXED:
                    $price += $tax->rate;
                    break;
                case Tax::TYPE_PERCENT:
                    $price += ($tax->rate / 100) * $price;
                    break;
            }
        }

        return $price - $total;
    }

    /**
     * Instantiate the general order details.
     * The $data parameter should be an array containing:
     *
     * - identifier (optional)
     * - currency (optional)
     * - payment (optional)
     * - shipping (optional)
     * - status (optional)
     *
     * See this class' protected property $data for reference.
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    protected static function setOrderData(array $data, Order $order = null)
    {
        if ($order && $order->exists) {
            self::$data = array_replace_recursive([
                'identifier' => $order->identifier,
                'currency' => $order->currency,
                'payment' => $order->payment,
                'shipping' => $order->shipping,
                'status' => $order->status,
            ], $data);

            return;
        }

        if (!isset($data['identifier']) || empty($data['identifier'])) {
            $data['identifier'] = str_random(20);

            while (static::where('identifier', $data['identifier'])->first()) {
                $data['identifier'] = str_random(20);
            }
        }

        if (!isset($data['currency']) || empty($data['currency'])) {
            $data['currency'] = config('shop.price.default_currency');
        }

        if (!isset($data['payment']) || empty($data['payment'])) {
            $data['payment'] = self::PAYMENT_CASH_DELIVERY;
        }

        if (!isset($data['shipping']) || empty($data['shipping'])) {
            $data['shipping'] = self::SHIPPING_PERSONAL_LIFTING;
        }

        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = self::STATUS_PENDING;
        }

        self::$data = array_replace_recursive(self::$data,
            array_intersect_key($data, self::$data)
        );
    }

    /**
     * Instantiate the customer details.
     * The $customer parameter should be an array containing:
     *
     * - first_name (required)
     * - last_name (required)
     * - email (required)
     * - phone (required)
     *
     * See this class' protected property $customer for reference.
     *
     * @param array $customer
     * @param Order $order
     * @return void
     * @throws OrderException
     */
    protected static function setOrderCustomer(array $customer, Order $order = null)
    {
        if ($order && $order->exists) {
            self::$customer = array_replace_recursive(
                (array)$order->customer, $customer
            );

            return;
        }

        if (!isset($customer['first_name']) || empty($customer['first_name'])) {
            throw OrderException::invalidCustomerFirstName();
        }

        if (!isset($customer['last_name']) || empty($customer['last_name'])) {
            throw OrderException::invalidCustomerLastName();
        }

        if (!isset($customer['email']) || empty($customer['email'])) {
            throw OrderException::invalidCustomerEmail();
        }

        if (!isset($customer['phone']) || empty($customer['phone'])) {
            throw OrderException::invalidCustomerPhone();
        }

        self::$customer = array_replace_recursive(self::$customer,
            array_intersect_key($customer, self::$customer)
        );
    }

    /**
     * Instantiate the order's addresses (shipping and delivery).
     * The $addresses parameter should be an array containing:
     *
     * - shipping (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     *
     * - delivery (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     *
     * See this class' protected property $addresses for reference.
     *
     * @param array $addresses
     * @param Order $order
     * @return void
     * @throws OrderException
     */
    protected static function setOrderAddresses(array $addresses, Order $order = null)
    {
        if ($order && $order->exists) {
            self::$addresses = array_replace_recursive(
                (array)$order->addresses, $addresses
            );

            return;
        }

        if (!isset($addresses['shipping']) || empty($addresses['shipping'])) {
            throw OrderException::invalidShippingDetails();
        }

        if (!isset($addresses['shipping']['city']) || empty($addresses['shipping']['city'])) {
            throw OrderException::invalidShippingCity();
        }

        if (!isset($addresses['shipping']['address']) || empty($addresses['shipping']['address'])) {
            throw OrderException::invalidShippingAddress();
        }

        if (!isset($addresses['delivery']) || empty($addresses['delivery'])) {
            throw OrderException::invalidDeliveryDetails();
        }

        if (!isset($addresses['delivery']['city']) || empty($addresses['delivery']['city'])) {
            throw OrderException::invalidDeliveryCity();
        }

        if (!isset($addresses['delivery']['address']) || empty($addresses['delivery']['address'])) {
            throw OrderException::invalidDeliveryAddress();
        }

        self::$addresses = array_replace_recursive(self::$addresses,
            array_intersect_key($addresses, self::$addresses)
        );
    }

    /**
     * Instantiate the order items.
     * The $items parameter should be an array containing:
     *
     * - 0 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - 1 (index key)
     * ----- product_id (required)
     * ----- quantity (required)
     * - etc.
     *
     * @param array $items
     * @param Order $order
     * @return void
     * @throws OrderException
     */
    protected static function setOrderItems(array $items, Order $order = null)
    {
        self::$items = [];

        if ($order && $order->exists) {
            foreach ($items as $item) {
                self::$items[] = array_intersect_key($item, [
                    'product_id' => null,
                    'quantity' => null,
                ]);
            }

            return;
        }

        if (!is_array($items) || empty($items)) {
            throw OrderException::noOrderItems();
        }

        foreach ($items as $item) {
            if (!isset($item['product_id'])) {
                throw OrderException::invalidOrderItem();
            }

            self::$items[] = array_intersect_key($item, [
                'product_id' => null,
                'quantity' => null,
            ]);
        }
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('identifier');
    }
}