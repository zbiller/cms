<?php

namespace App\Mail;

use App\Models\Cms\Email;
use App\Models\Shop\Cart;
use App\Models\Shop\Currency;
use App\Exceptions\EmailException;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCartReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email model.
     *
     * @var Email
     */
    protected $email;

    /**
     * The loaded user model.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * The loaded user cart.
     *
     * @var Cart
     */
    public $cart;

    /**
     * Create a new message instance.
     *
     * @param $identifier
     * @param Authenticatable $user
     * @param Cart $cart
     * @throws EmailException
     */
    public function __construct($identifier, Authenticatable $user, Cart $cart)
    {
        $this->email = Email::findByIdentifier($identifier);
        $this->user = $user;
        $this->cart = $cart;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws EmailException
     */
    public function build()
    {
        $this->replyTo($this->email->reply_to);
        $this->from($this->email->from_address, $this->email->from_name);
        $this->subject($this->email->subject ?: 'Cart Contents Reminder');

        $this->markdown($this->email->getView(), [
            'message' => $this->parseMessage(),
        ]);

        return $this;
    }

    /**
     * Parse the message for used variables.
     * Replace the variable names with the relevant content.
     *
     * @return mixed
     */
    private function parseMessage()
    {
        $message = $this->email->message;

        $message = str_replace(
            '[first_name]',
            $this->user->first_name,
            $message
        );

        $message = str_replace(
            '[last_name]',
            $this->user->last_name,
            $message
        );

        $message = str_replace(
            '[full_name]',
            $this->user->full_name,
            $message
        );

        $message = str_replace(
            '[home_url]',
            url('/'),
            $message
        );

        $message = str_replace(
            '[cart_contents]',
            $this->getCartContents(),
            $message
        );

        return $message;
    }

    /**
     * Get the HTML table representing the cart's products and grand total.
     *
     * @return string
     */
    private function getCartContents()
    {
        $html = [];

        $html[] = '<table class="table" cellspacing="0" width="100%">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="header-cell" align="left"><strong>Product</strong></th>';
        $html[] = '<th class="header-cell" align="center"><strong>Quantity</strong></th>';
        $html[] = '<th class="header-cell" align="right"><strong>Price</strong></th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        foreach ($this->cart->items()->get() as $item) {
            $html[] = '<tr>';
            $html[] = '<td align="left" class="content-cell">' . $item->product->name . '</td>';
            $html[] = '<td align="center" class="content-cell">' . $item->quantity . '</td>';
            $html[] = '<td align="right" class="content-cell">' . number_format($item->quantity * Currency::convert($item->product->final_price, $item->product->currency->code, config('shop.price.default_currency')), 2) . ' ' . config('shop.price.default_currency') . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '<tfoot>';
        $html[] = '<tr>';
        $html[] = '<td align="left" class="footer-cell"><strong>Total</strong></td>';
        $html[] = '<td class="footer-cell"></td>';
        $html[] = '<td align="right" class="footer-cell"><strong>' . number_format($this->cart->grand_total, 2) . ' ' . config('shop.price.default_currency') . '</strong></td>';
        $html[] = '</tr>';
        $html[] = '</tfoot>';
        $html[] = '</table>';

        return implode('', $html);
    }
}