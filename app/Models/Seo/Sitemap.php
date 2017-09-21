<?php

namespace App\Models\Seo;

use App\Exceptions\SitemapException;
use App\Models\Cms\Url;
use Carbon\Carbon;
use Exception;
use File;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Sitemap as SitemapTag;
use Spatie\Sitemap\Tags\Url as UrlTag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class Sitemap
{
    /**
     * The sitemap generator handler.
     *
     * @var SitemapGenerator
     */
    protected $sitemap;

    /**
     * The sitemap index generator handler.
     *
     * @var SitemapIndex
     */
    protected $index;

    /**
     * The base url on which the crawling will happen.
     * This is always the config setting "app.name".
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The collection of urls that were found during crawling.
     *
     * @var Collection
     */
    protected $crawledUrls;

    /**
     * The collection of urls from the database table called "urls".
     *
     * @var Collection
     */
    protected $databaseUrls;

    /**
     * An array representing the links that should't be crawled by the SitemapGenerator.
     *
     * @var array
     */
    protected $nonCrawlableUrls = [];

    /**
     * The namespace used for the "main" sitemap file name.
     *
     * @var string
     */
    protected $mainNamespace = 'sitemap';

    /**
     * The namespace used for the "crawled urls" sitemap file name.
     *
     * @var string
     */
    protected $crawledUrlsNamespace = 'sitemap_crawled_urls';

    /**
     * The namespace used for the "database urls" sitemap files names.
     *
     * @var string
     */
    protected $databaseUrlsNamespace = 'sitemap_database_urls';

    /**
     * Construct the class.
     *
     * @set $baseUrl
     * @set $sitemap
     */
    public function __construct()
    {
        $this->baseUrl = config('app.url');
    }

    /**
     * Generate sitemaps for all urls present inside the application: crawled, database, etc.
     * In the end, concatenate all the generated sitemaps into a sitemap index called "sitemap.xml".
     *
     * @return bool
     * @throws SitemapException
     */
    public function generateAllXmlFiles()
    {
        try {
            $this->crawledUrls = collect();
            $this->databaseUrls = Url::all();

            $this->generateXmlForCrawledUrls();
            $this->generateXmlForDatabaseUrls();

            $this->buildSitemapIndex();
        } catch (Exception $e) {
            throw SitemapException::xmlGenerationFailed();
        }
    }

    /**
     * Download all sitemap xml files as an archive called "sitemap.zip"
     *
     * @param string $file
     * @return BinaryFileResponse
     * @throws SitemapException
     */
    public function downloadXmlFile($file)
    {
        if (!File::exists(public_path($file))) {
            throw SitemapException::xmlFileNotFound();
        }

        return response()->download($file);
    }

    /**
     * Download all sitemap xml files as an archive called "sitemap.zip"
     *
     * @return BinaryFileResponse
     */
    public function downloadAllXmlFiles()
    {
        $name = 'sitemap.zip';
        $zip = new ZipArchive;
        $zip->open($name, ZipArchive::CREATE);

        foreach (File::glob('sitemap*.xml') as $file) {
            $zip->addFile(public_path($file), basename($file));
        }

        $zip->close();

        return response()->download($name)->deleteFileAfterSend(true);
    }

    /**
     * Remove a single sitemap xml file from disk.
     *
     * @param string $file
     * @throws SitemapException
     */
    public function removeXmlFile($file)
    {
        if (!File::exists(public_path($file))) {
            throw SitemapException::xmlFileNotFound();
        }

        File::delete(public_path($file));

        $this->buildSitemapIndex();
    }

    /**
     * Remove all sitemap xml files from disk.
     *
     * @return void
     */
    public function removeAllXmlFiles()
    {
        File::delete(File::glob('sitemap*.xml'));
    }

    /**
     * Generate the sitemap.xml file responsible for holding the crawled urls.
     * Meaning the urls that are linked throughout the site, but they don't exist in database.
     *
     * @return void
     */
    public function generateXmlForCrawledUrls()
    {
        $this->nonCrawlableUrls = array_merge(
            $this->nonCrawlableUrls, $this->databaseUrls->pluck('url')->toArray()
        );

        $this->sitemap = SitemapGenerator::create($this->baseUrl);
        $this->sitemap->hasCrawled(function (UrlTag $url) {
            if (!$this->urlShouldBeCrawled($url->url)) {
                return;
            }

            $this->crawledUrls->push($url);

            return $url;
        })->writeToFile(
            public_path("{$this->crawledUrlsNamespace}.xml")
        );
    }

    /**
     * Generate the sitemap.xml files responsible for holding urls coming from database. (table "urls")
     * These urls are inserted into xml files in chunks of 10000, thus avoiding sitemap.xml restrictions.
     *
     * @return void
     */
    public function generateXmlForDatabaseUrls()
    {
        foreach ($this->databaseUrls->chunk(10000) as $chunkIndex => $databaseUrlChunk) {
            $this->sitemap = SitemapGenerator::create($this->baseUrl);
            $this->sitemap->hasCrawled(function (UrlTag $url) {
                return;
            });

            foreach ($databaseUrlChunk as $databaseUrl) {
                $url = UrlTag::create(url($databaseUrl->url))
                    ->setLastModificationDate($databaseUrl->updated_at)
                    ->setChangeFrequency('daily');

                if ($databaseUrl->url == '/') {
                    $url->setPriority(1);
                } else {
                    switch (substr_count($databaseUrl->url, '/')) {
                        case 0:
                            $url->setPriority(1);
                            break;
                        case 1:
                            $url->setPriority(0.9);
                            break;
                        default:
                            $url->setPriority(0.8);
                            break;
                    }
                }

                $this->sitemap->getSitemap()->add($url);
            }

            $this->sitemap->writeToFile(
                public_path("{$this->databaseUrlsNamespace}_{$chunkIndex}.xml")
            );
        }
    }

    /**
     * Concatenate all xml files generated throughout the application into a single "sitemap index".
     * The "sitemap index" file is called "sitemap.xml" and will act as the main sitemap representation for Google.
     *
     * @return void
     */
    public function buildSitemapIndex()
    {
        if (!($this->sitemap instanceof SitemapGenerator)) {
            $this->sitemap = SitemapGenerator::create($this->baseUrl);
        }

        $this->index = SitemapIndex::create();

        if (File::exists(public_path("{$this->crawledUrlsNamespace}.xml"))) {
            $this->index->add(
                SitemapTag::create("{$this->crawledUrlsNamespace}.xml")
                    ->setLastModificationDate(Carbon::now())
            );
        }

        foreach (File::glob(public_path("{$this->databaseUrlsNamespace}_*.xml")) as $xml) {
            if (File::exists($xml)) {
                $this->index->add(basename($xml));
            }
        }

        $this->index->writeToFile(public_path("{$this->mainNamespace}.xml"));
    }

    /**
     * Verify if a given url should be crawled.
     * The condition is that the urls must not exist in the database. (table "urls")
     *
     * @param string $url
     * @return bool
     */
    protected function urlShouldBeCrawled($url)
    {
        return
            ($path = str_replace($this->baseUrl, '', $url)) &&
            $path != '/' && !in_array(trim($path, '/'), $this->nonCrawlableUrls);
    }
}