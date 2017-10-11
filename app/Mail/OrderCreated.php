<?php

namespace App\Mail;

use App\Exceptions\EmailException;
use App\Models\Cms\Email;
use App\Models\Shop\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email model.
     *
     * @var Email
     */
    protected $email;

    /**
     * The loaded order model.
     *
     * @var Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @param $identifier
     * @param Order $order
     * @throws EmailException
     */
    public function __construct($identifier, Order $order)
    {
        $this->email = Email::findByIdentifier($identifier);
        $this->order = $order;
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
        $this->subject($this->email->subject ?: 'Thank you for your purchase!');

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
            $this->order->first_name,
            $message
        );

        $message = str_replace(
            '[last_name]',
            $this->order->last_name,
            $message
        );

        $message = str_replace(
            '[full_name]',
            $this->order->full_name,
            $message
        );

        $message = str_replace(
            '[order_id]',
            $this->order->identifier,
            $message
        );

        $message = str_replace(
            '[order_status]',
            Order::$statuses[$this->order->status],
            $message
        );

        $message = str_replace(
            '[order_contents]',
            $this->getOrderContents(),
            $message
        );

        return $message;
    }

    /**
     * Get the HTML table representing the cart's products and grand total.
     *
     * @return string
     */
    private function getOrderContents()
    {
        return view()->make('emails.variables.order_contents')->with([
            'order' => $this->order
        ])->render();
    }
}