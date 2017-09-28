<?php

namespace App\Links;

use App\Models\Seo\Redirect;
use Illuminate\Contracts\Logging\Log;
use Spatie\Crawler\Url;
use Spatie\LinkChecker\Reporters\BaseReporter;

class BrokenLinksRedirectReporter extends BaseReporter
{
    /**
     * The redirect model to be used for database operations.
     *
     * @var Redirect
     */
    protected $redirect;

    /**
     * Set the redirect model.
     *
     * @param Redirect $redirect
     */
    public function __construct(Redirect $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * Called when the crawler has crawled the given url.
     *
     * @param \Spatie\Crawler\Url $url
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @param \Spatie\Crawler\Url $foundOnUrl
     * @return string
     */
    public function hasBeenCrawled(Url $url, $response, Url $foundOnUrl = null)
    {
        $statusCode = parent::hasBeenCrawled($url, $response);

        if ($this->isSuccessOrRedirect($statusCode)) {
            return;
        }

        $path = trim($url->path(), '/');

        if ($this->redirect->where('old_url', $path)->first()) {
            return;
        }

        $this->redirect->create([
            'old_url' => $path,
            'status' => Redirect::STATUS_NORMAL,
        ]);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling() {}
}
