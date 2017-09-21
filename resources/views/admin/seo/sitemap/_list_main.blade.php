<section class="list" style="margin-bottom: 20px;">
    <table cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <td>Main Sitemap File</td>
                <td class="actions-sitemap">Actions</td>
            </tr>
        </thead>
        <tbody>
        @if(File::exists(public_path("{$mainSitemapNamespace}.xml")))
            <tr>
                <td>
                    <span class="flag green" style="margin-left: 0;">sitemap file exists</span>
                </td>
                <td>
                    {!! button()->downloadFile(route('admin.sitemap.download', $mainSitemapNamespace . '.xml')) !!}
                    {!! button()->viewRecord(url("{$mainSitemapNamespace}.xml"), ['target' => '_blank']) !!}
                    {!! button()->deleteRecord(route('admin.sitemap.destroy', $mainSitemapNamespace . '.xml')) !!}
                </td>
            </tr>
        @else
            <tr>
                <td colspan="10" style="padding: 10px 15px;">
                    <span class="flag red" style="margin-left: 0;">sitemap file does not exist</span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</section>