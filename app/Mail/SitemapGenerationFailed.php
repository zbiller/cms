<?php

namespace App\Mail;

use App\Exceptions\EmailException;
use App\Models\Shop\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SitemapGenerationFailed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The error message the GenerateSitemapXml produced.
     *
     * @var string
     */
    public $message;

    /**
     * Set the error message.
     *
     * @param string|null $message
     */
    public function __construct($message = null)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws EmailException
     */
    public function build()
    {
        $this
            ->from(setting()->value('company-email'), setting()->value('company-name'))
            ->subject('Sitemap generation has failed!')
            ->markdown('emails.sitemap_generation_failed');

        return $this;
    }
}