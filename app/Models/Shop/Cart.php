<?php

namespace App\Models\Shop;

use App\Events\CartReminded;
use App\Exceptions\CartException;
use App\Exceptions\OrderException;
use App\Models\Auth\User;
use App\Models\Model;
use App\Models\Shop\Cart\Item;
use App\Scopes\WithCartTotalAndCountScope;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class Cart extends Model
{
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'carts';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_token',
        'identifier',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'user',
    ];

    /**
     * The constants defining the type of a "total".
     *
     * @const
     */
    const TOTAL_RAW = 1;
    const TOTAL_SUB = 2;
    const TOTAL_GRAND = 3;

    /**
     * The cart instance identified for the current user browsing.
     *
     * @var Cart
     */
    protected static $cart;

    /**
     * The authenticated user, if any.
     *
     * @var User
     */
    protected static $user;

    /**
     * The user token generated for un-authenticated users.
     *
     * @var string
     */
    protected static $token;

    /**
     * The cart's identifier value.
     *
     * @var string
     */
    protected static $identifier;

    /**
     * Boot the model.
     * After deleting a cart instance, update the products quantities.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new WithCartTotalAndCountScope);

        static::deleting(function (Cart $cart) {
            foreach ($cart->items()->get() as $item) {
                $product = $item->product;
                $product->quantity += $item->quantity;

                $product->save();
            }
        });
    }

    /**
     * A cart belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A cart has many items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'cart_id');
    }

    /**
     * Filter the query by identifier.
     *
     * @param Builder $query
     * @param $identifier
     */
    public function scopeWhereIdentifier($query, $identifier)
    {
        $query->where('identifier', $identifier);
    }

    /**
     * Filter the query by user.
     *
     * @param Builder $query
     * @param $user
     */
    public function scopeWhereUser($query, $user)
    {
        $query->where('user_id', $user);
    }

    /**
     * Filter the query by existing users.
     *
     * @param Builder $query
     */
    public function scopeOnlyUsers($query)
    {
        $query->whereNotNull('user_id');
    }

    /**
     * Filter the query by guests.
     *
     * @param Builder $query
     */
    public function scopeOnlyGuests($query)
    {
        $query->whereNull('user_id');
    }

    /**
     * Override route model binding default column value.
     * This is done because the cart is joined with items, products and currencies by the global scope.
     * Otherwise, the model binding will throw an "ambiguous column" error.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getTable() . '.' . $this->getKeyName();
    }

    /**
     * Get the raw total of a given cart instance.
     *
     * @return float
     */
    public function getTotalAttribute()
    {
        return static::getTotal(null, static::TOTAL_RAW, $this);
    }

    /**
     * Get the raw total of a given cart instance.
     *
     * @return float
     */
    public function getRawTotalAttribute()
    {
        return static::getTotal(null, static::TOTAL_RAW, $this);
    }

    /**
     * Get the grand total of a given cart instance.
     *
     * @return float
     */
    public function getSubTotalAttribute()
    {
        $total = static::getTotal(null, static::TOTAL_SUB, $this);
        $discounts = static::getDiscountTotal(null, $this);

        return $total - $discounts;
    }

    /**
     * Get the grand total of a given cart instance.
     *
     * @return float
     */
    public function getGrandTotalAttribute()
    {
        $total = static::getTotal(null, static::TOTAL_GRAND, $this);
        $discounts = static::getDiscountTotal(null, $this);
        $taxes = static::getTaxTotal(null, $this);

        return $total - $discounts + $taxes;
    }

    /**
     * Get the correct cart instance for the user if exists.
     * Otherwise create a new one to be used.
     *
     * @return Cart
     */
    public static function getCart()
    {
        if (self::$cart) {
            return self::$cart;
        }

        if (auth()->check()) {
            self::$user = auth()->user();
        } else {
            self::$token = static::getToken();
        }

        self::$identifier = static::getIdentifier();

        if (!(self::$cart = static::whereIdentifier(self::$identifier)->first())) {
            self::$cart = static::create([
                'user_id' => self::$user ? self::$user->id : null,
                'user_token' => self::$token ?: null,
                'identifier' => self::$identifier,
            ]);
        }

        return self::$cart;
    }

    /**
     * Get the cart's total price (raw | sub | grand).
     * Depending on the type specified, the "raw total", "sub total" or "grand_total" will be returned.
     *
     * @param string|null $currency
     * @param int $type
     * @param Cart $cart
     * @return float|int
     */
    public static function getTotal($currency = null, $type, Cart $cart = null)
    {
        if ($currency === null) {
            $currency = config('shop.price.default_currency');
        }

        $total = 0;

        if ($cart && $cart->exists) {
            self::$cart = $cart;
        }

        if (!self::$cart) {
            self::$cart = static::getCart();
        }

        foreach (self::$cart->items()->get() as $item) {
            switch ($type) {
                case static::TOTAL_GRAND;
                    $price = $item->product->final_price;
                    break;
                case static::TOTAL_SUB;
                    $price = $item->product->price_with_discounts;
                    break;
                default:
                    $price = $item->product->price;
                    break;
            }

            $total += Currency::convert(
                    $price, $item->product->currency->code, $currency
                ) * $item->quantity;
        }

        return $total;
    }

    /**
     * Get the cart's raw total price.
     * The total represented by the "raw total" does not include discounts or taxes.
     *
     * @param string|null $currency
     * @return float|int
     */
    public static function getRawTotal($currency = null)
    {
        return static::getTotal($currency, static::TOTAL_RAW);
    }

    /**
     * Get the cart's sub total price.
     * The total represented by the "sub total" only includes discounts, but not taxes.
     *
     * @param string|null $currency
     * @return float|int
     */
    public static function getSubTotal($currency = null)
    {
        $total = static::getTotal($currency, static::TOTAL_SUB);
        $discounts = static::getDiscountTotal($currency);

        return $total - $discounts;
    }

    /**
     * Get the cart's final total price.
     * The total represented by the "grand total" also includes discounts and taxes.
     *
     * @param string|null $currency
     * @return float|int
     */
    public static function getGrandTotal($currency = null)
    {
        $total = static::getTotal($currency, static::TOTAL_GRAND);
        $discounts = static::getDiscountTotal($currency);
        $taxes = static::getTaxTotal($currency);

        return $total - $discounts + $taxes;
    }

    /**
     * Get the cart's discount value based on discounts applicable on order only.
     *
     * @param string null $currency
     * @param Cart $cart
     * @return float|int|null
     */
    public static function getDiscountTotal($currency = null, Cart $cart = null)
    {
        if ($currency === null) {
            $currency = config('shop.price.default_currency');
        }

        $total = static::getTotal($currency, static::TOTAL_GRAND, $cart);
        $price = $total;

        foreach (Discount::forOrder()->onlyActive()->get() as $discount) {
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
     * Get the cart's tax value based on taxes applicable on order only.
     *
     * @param string null $currency
     * @param Cart $cart
     * @return float|int|null
     */
    public static function getTaxTotal($currency = null, Cart $cart = null)
    {
        if ($currency === null) {
            $currency = config('shop.price.default_currency');
        }

        $total = static::getTotal($currency, static::TOTAL_GRAND, $cart);
        $price = $total;

        foreach (Tax::forOrder()->onlyActive()->get() as $tax) {
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
     * Get the number of different items in a cart instance.
     *
     * @return int
     */
    public static function getItemsCount()
    {
        if (!self::$cart) {
            self::$cart = static::getCart();
        }

        return self::$cart->items()->count();
    }

    /**
     * Add a product to a cart instance.
     *
     * @param Product $product
     * @param int $quantity
     * @return bool
     * @throws CartException
     */
    public static function addProduct(Product $product, $quantity = 1)
    {
        if (!$product->exists) {
            throw CartException::invalidProduct();
        }

        if ($product->quantity < $quantity) {
            throw CartException::quantityExceeded();
        }

        try {
            if (!self::$cart) {
                self::$cart = static::getCart();
            }

            return DB::transaction(function () use ($product, $quantity) {
                if ($item = self::$cart->items()->where('product_id', $product->id)->first()) {
                    $item->update([
                        'quantity' => $item->quantity + $quantity,
                    ]);
                } else {
                    self::$cart->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity
                    ]);
                }

                $product->quantity -= $quantity;
                $product->save();

                return true;
            });
        } catch (Exception $e) {
            throw CartException::productAddFailed();
        }
    }

    /**
     * Add a product to a cart instance.
     *
     * @param Product $product
     * @param int $quantity
     * @return bool
     * @throws CartException
     */
    public static function updateProduct(Product $product, $quantity)
    {
        if (!$product->exists) {
            throw CartException::invalidProduct();
        }

        try {
            if (!self::$cart) {
                self::$cart = static::getCart();
            }

            return DB::transaction(function () use ($product, $quantity) {
                if ($item = self::$cart->items()->where('product_id', $product->id)->first()) {
                    if ($quantity > $item->quantity) {
                        $product->quantity -= $quantity - $item->quantity;
                    } elseif ($quantity < $item->quantity) {
                        $product->quantity += $item->quantity - $quantity;
                    }

                    $item->update([
                        'quantity' => $quantity
                    ]);

                    $product->save();
                }

                return true;
            });
        } catch (Exception $e) {
            throw CartException::productUpdateFailed();
        }
    }

    /**
     * Remove a product from a cart instance.
     *
     * @param Product $product
     * @return bool
     * @throws CartException
     */
    public static function removeProduct(Product $product)
    {
        if (!$product->exists) {
            throw CartException::invalidProduct();
        }

        try {
            if (!self::$cart) {
                self::$cart = static::getCart();
            }

            return DB::transaction(function () use ($product) {
                if ($item = self::$cart->items()->where('product_id', $product->id)->first()) {
                    $product->quantity += $item->quantity;

                    $product->save();
                    $item->delete();
                }

                return true;
            });
        } catch (Exception $e) {
            throw CartException::productRemoveFailed();
        }
    }

    /**
     * Remove all products from a cart instance.
     *
     * @return bool
     * @throws CartException
     */
    public static function emptyCart()
    {
        try {
            if (!self::$cart) {
                self::$cart = static::getCart();
            }

            return DB::transaction(function () {
                foreach (self::$cart->items()->get() as $item) {
                    $product = $item->product;
                    $product->quantity += $item->quantity;

                    $product->save();
                    $item->delete();
                }

                return true;
            });
        } catch (Exception $e) {
            throw CartException::productRemoveFailed(true);
        }
    }

    /**
     * Create a new order based on the current cart instance.
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
     * - billing (required)
     * ----- country (optional)
     * ----- state (optional)
     * ----- city (required)
     * ----- address (required)
     *
     * @param array $data
     * @param array $customer
     * @param array $addresses
     * @return Order
     * @throws \App\Exceptions\OrderException
     */
    public static function placeOrder(array $data, array $customer, array $addresses)
    {
        if (!self::$cart) {
            self::$cart = static::getCart();
        }

        $items = [];

        foreach (self::$cart->items as $item) {
            $items[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ];
        }

        try {
            return DB::transaction(function () use ($data, $customer, $addresses, $items) {
                $order = Order::createOrder($data, $customer, $addresses, $items);

                //self::$cart->delete();

                return $order;
            });
        } catch (OrderException $e) {
            throw $e;
        } catch (Exception $e) {
            throw OrderException::createOrderFailed();
        }
    }

    /**
     * Attempt to clean old shopping carts.
     *
     * A cart qualifies as being old if:
     * "created_at" field is smaller than the current date minus the number of days set in the
     * "cart.delete_records_older_than" key of config/shop.php file.
     *
     * @return bool|void
     * @throws CartException
     */
    public static function cleanOld()
    {
        set_time_limit(0);

        $days = (int)config('shop.cart.delete_records_older_than');

        if (!($days > 0)) {
            return;
        }

        try {
            DB::transaction(function () use ($days) {
                $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');

                foreach (static::where('carts.created_at', '<', $date)->get() as $cart) {
                    $cart->delete();
                }
            });

            return true;
        } catch (Exception $e) {
            throw CartException::cleanupFailed();
        }
    }

    /**
     * Attempt to clean old shopping carts.
     *
     * A cart qualifies as being old if:
     * "created_at" field is smaller than the current date minus the number of days set in the
     * "cart.delete_records_older_than" key of config/shop.php file.
     *
     * @return bool|void
     * @throws CartException
     */
    public static function sendReminders()
    {
        event(new CartReminded);
    }

    /**
     * Get a new or existing identifier for the cart.
     *
     * @return mixed|string
     */
    private static function getIdentifier()
    {
        $identifier = str_random(10);
        $condition = null;

        if (self::$user) {
            $condition = ['user_id' => self::$user->id];
        } elseif (self::$token) {
            $condition = ['user_token' => self::$token];
        }

        if ($cart = static::where($condition)->first()) {
            return $cart->identifier;
        }

        while (static::whereIdentifier($identifier)->count() > 0) {
            $identifier = str_random(20);
        }

        return $identifier;
    }

    /**
     * Get a new or existing unique cart token from the user's cookie storage.
     *
     * @return string
     */
    private static function getToken()
    {
        if (isset($_COOKIE['cart_user_token'])) {
            return $_COOKIE['cart_user_token'];
        }

        $token = str_random(20);

        while (static::where('user_token', $token)->count() > 0) {
            $token = str_random(20);
        }

        setcookie('cart_user_token', $token, time()+ 60 * 60 * 24 * 30 * 12, '/');

        return $token;
    }
}