<?php

namespace App\Jobs;

use App\Models\Seo\Sitemap;
use App\Notifications\SitemapGenerationFailed;
use App\Notifications\SitemapGenerationSuccessful;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * The currently authenticated user.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Set the currently authenticated user.
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Sitemap $sitemap)
    {
        $sitemap->generateAllXmlFiles();

        $this->user->notify(new SitemapGenerationSuccessful);
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->user->notify(new SitemapGenerationFailed($exception->getMessage()));
    }
}
