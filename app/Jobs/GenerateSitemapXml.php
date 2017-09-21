<?php

namespace App\Jobs;

use App\Mail\SitemapGeneration;
use App\Models\Seo\Sitemap;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class GenerateSitemapXml implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Sitemap $sitemap)
    {
        $sitemap->generateAllXmlFiles();

        Mail::to(setting()->value('company-email'))->send(new SitemapGeneration(true));
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Mail::to(setting()->value('company-email'))->send(new SitemapGeneration(false));
    }
}
