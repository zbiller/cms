<?php

namespace App\Mail;

use App\Exceptions\EmailException;
use App\Models\Cms\Email;
use App\Models\Shop\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CartReminder extends Mailable implements ShouldQueue
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
        return view()->make('emails.variables.cart_contents')->with([
            'cart' => $this->cart
        ])->render();
    }
}