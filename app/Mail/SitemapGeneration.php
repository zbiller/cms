<?php

namespace App\Mail;

use App\Exceptions\EmailException;
use App\Models\Shop\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SitemapGeneration extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The status of the sitemap generation process.
     * "true" = success | "false" = fail
     *
     * @var bool
     */
    public $status = false;

    /**
     * Create a new message instance.
     *
     * @param bool $status
     */
    public function __construct($status = false)
    {
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws EmailException
     */
    public function build()
    {
        $this->from(setting()->value('company-email'), setting()->value('company-name'));
        $this->subject('Sitemap generation has ' . ($this->status === true ? 'completed successfully!' : 'failed.'));
        $this->markdown('emails.sitemap_generation');

        return $this;
    }
}