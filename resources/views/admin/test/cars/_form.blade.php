{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('slug') !!}
    {!! uploader()->field('image')->label('Image')->model($item)->types('image', 'video', 'audio', 'file')->manager() !!}
    {!! uploader()->field('video')->label('Video')->model($item)->types('video')->manager() !!}
    {!! uploader()->field('audio')->label('Audio')->model($item)->types('audio')->manager() !!}
    {!! uploader()->field('file')->label('File')->model($item)->types('file')->manager() !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->select('owner_id', 'Owner', $owners->pluck('full_name', 'id')) !!}
    {!! form_admin()->select('brand_id', 'Brand', $brands->pluck('name', 'id')) !!}

</div>
<div id="tab-3" class="tab">
    {!! form_admin()->text('metadata[title]', 'Title') !!}
    {!! form_admin()->text('metadata[subtitle]', 'Subtitle') !!}
    {!! form_admin()->editor('metadata[content]', 'Content') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\CarRequest::class, '.form') !!}
@append