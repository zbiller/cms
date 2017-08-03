<?php

namespace App\Services;

use App\Contracts\BuyableContract;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * @var Collection
     */
    public $items;

    /**
     * @var SessionManager
     */
    protected $session;

    /**
     * @var int
     */
    public $quantity = 0;

    /**
     * @var int
     */
    public $price = 0;

    /**
     * @param SessionManager $session
     */
    public function __construct(SessionManager $session)
    {
        $this->session = $session;
        $this->identifier = str_random(20);
    }

    public function add(BuyableContract $model, $quantity = 1)
    {
        $id = $model->{$model->getBuyableIdentifier()};

        $this->items[$id] = [
            'price' => $model->getBuyablePrice(),
            'quantity' => isset($this->items[$id]) ? $this->items[$id]['quantity'] + $quantity : $quantity,
            'data' => $model->getBuyableContents(),
        ];

        $this->session->put('cart', collect($this->items));
    }
}