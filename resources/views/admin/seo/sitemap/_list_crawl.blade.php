<section class="list" style="margin-bottom: 20px;">
    <table cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <td>Crawled Links Sitemap Files</td>
                <td class="actions-sitemap">Actions</td>
            </tr>
        </thead>
        <tbody>
        @if(File::exists(public_path("{$crawledSitemapUrlsNamespace}.xml")))
            <tr>
                <td>
                    Crawled Urls Sitemap
                </td>
                <td>
                    {!! button()->downloadFile(route('admin.sitemap.download', $crawledSitemapUrlsNamespace . '.xml')) !!}
                    {!! button()->viewRecord(url("{$crawledSitemapUrlsNamespace}.xml"), ['target' => '_blank']) !!}
                    {!! button()->deleteRecord(route('admin.sitemap.destroy', $crawledSitemapUrlsNamespace . '.xml')) !!}
                </td>
            </tr>
        @else
            <tr>
                <td colspan="10" style="padding: 10px 15px;">
                    No sitemap file for crawled links
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</section>