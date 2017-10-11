<?php

namespace App\Mail;

use App\Exceptions\EmailException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SitemapGenerationSuccessful extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
            ->subject('Sitemap generation has completed successfully!')
            ->markdown('emails.sitemap_generation_successful');

        return $this;
    }
}