<?php

namespace App\Models\Shop;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Model;
use App\Models\Auth\User;
use App\Models\Shop\Cart\Item;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Scopes\WithCartTotalAndCountScope;
use App\Exceptions\CartException;

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
            foreach ($cart->items()->with('product')->get() as $item) {
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
     * A cart has many cart items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'cart_id');
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
     * Get the correct cart instance for the user if exists.
     * Otherwise create a new one to be used.
     *
     * @return Cart
     */
    public static function getCart()
    {
        if (auth()->check()) {
            self::$user = auth()->user();
        } else {
            self::$token = static::getToken();
        }

        self::$identifier = static::getIdentifier();

        if (!(self::$cart = static::where('identifier', self::$identifier)->first())) {
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
     * @return float|int
     */
    public static function getTotal($currency = null, $type)
    {
        if ($currency === null) {
            $currency = config('shop.price.default_currency');
        }

        $total = 0;

        if (!self::$cart) {
            self::$cart = static::getCart();
        }

        foreach (self::$cart->items()->with('product')->get() as $item) {
            $product = $item->product;

            switch ($type) {
                case static::TOTAL_GRAND;
                    $price = $product->final_price;
                    break;
                case static::TOTAL_SUB;
                    $price = $product->price_with_discounts;
                    break;
                default:
                    $price = $product->price_with_discounts;
                    break;
            }

            $total += Currency::convert(
                $price, $product->currency->code, $currency
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
        return static::getTotal($currency, static::TOTAL_SUB);
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
        return static::getTotal($currency, static::TOTAL_GRAND);
    }

    /**
     * Get the number of different items in a cart instance.
     *
     * @return int
     */
    public static function getProductsCount()
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
            throw CartException::invalidProductInstance();
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
            throw new CartException(
                'Could not add the product to cart!'
            );
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
            throw CartException::invalidProductInstance();
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
            throw new CartException(
                'Could not update the product from cart!'
            );
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
            throw CartException::invalidProductInstance();
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
            throw new CartException(
                'Could not remove the product from cart!'
            );
        }
    }

    /**
     * Remove all products from a cart instance.
     *
     * @return bool
     * @throws CartException
     */
    public static function removeAllProducts()
    {
        try {
            if (!self::$cart) {
                self::$cart = static::getCart();
            }

            return DB::transaction(function () {
                foreach (self::$cart->items()->with('product')->get() as $item) {
                    $product = $item->product;
                    $product->quantity += $item->quantity;

                    $product->save();
                    $item->delete();
                }

                return true;
            });
        } catch (Exception $e) {
            throw new CartException(
                'Could not remove the products from cart!'
            );
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
            throw new CartException(
                'Could not clean up the old carts! Please try again.', $e->getCode(), $e
            );
        }
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

        while (static::where('identifier', $identifier)->count() > 0) {
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