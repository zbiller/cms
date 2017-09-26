<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Exceptions\SitemapException;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSitemapXml;
use App\Models\Seo\Sitemap;
use Exception;

class SitemapController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.seo.sitemap.index')->with([
            'mainSitemapNamespace' => 'sitemap',
            'crawledSitemapUrlsNamespace' => 'sitemap_crawled_urls',
            'databaseSitemapUrlsNamespace' => 'sitemap_database_urls',
        ]);
    }

    /**
     * @param Sitemap $sitemap
     * @param string|null $file
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Sitemap $sitemap, $file = null)
    {
        try {
            return $file ?
                $sitemap->downloadXmlFile($file) :
                $sitemap->downloadAllXmlFiles();
        } catch (SitemapException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.sitemap.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate()
    {
        try {
            dispatch(new GenerateSitemapXml(auth()->user()));

            flash()->success(
                'The sitemap file is being generated!<br /><br />' . PHP_EOL .
                'You will receive an email when the sitemap files have finished generating.'
            );
        } catch (SitemapException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            dd($e);
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.sitemap.index');
    }

    /**
     * @param Sitemap $sitemap
     * @param string|null $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Sitemap $sitemap, $file = null)
    {
        try {
            $sitemap->removeXmlFile($file);

            flash()->success('The record was successfully deleted!');
        } catch (SitemapException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.sitemap.index');
    }

    /**
     * @param Sitemap $sitemap
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(Sitemap $sitemap)
    {
        try {
            $sitemap->removeAllXmlFiles();

            flash()->success('All records were successfully deleted!');
        } catch (SitemapException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
        }

        return redirect()->route('admin.sitemap.index');
    }
}