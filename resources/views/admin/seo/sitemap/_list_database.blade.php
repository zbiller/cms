<section class="list">
    <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <td>Database Links Sitemap Files</td>
                <td class="actions-sitemap">Actions</td>
            </tr>
        </thead>
        <tbody>
        @if(($files = File::glob("{$databaseSitemapUrlsNamespace}_*.xml")) && count($files) > 0)
            @foreach($files as $index => $file)
                <tr>
                    <td>
                        Database Urls Sitemap {{ $index + 1 }}
                    </td>
                    <td>
                        {!! button()->downloadFile(route('admin.sitemap.download', $file)) !!}
                        {!! button()->viewRecord(url($file), ['target' => '_blank']) !!}
                        {!! button()->deleteRecord(route('admin.sitemap.destroy', $file)) !!}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" style="padding: 10px 15px;">
                    No sitemap files for database links
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</section>