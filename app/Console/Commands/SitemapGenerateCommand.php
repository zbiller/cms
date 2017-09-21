<?php

namespace App\Console\Commands;

use App\Models\Seo\Sitemap;
use Illuminate\Console\Command;

class SitemapGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap xml files.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Sitemap $sitemap)
    {
        $sitemap->generateAllXmlFiles();

        $this->info("Sitemap xml files have been generated successfully!");
    }
}