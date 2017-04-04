{!! validation()->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! form_admin()->color('content', 'Content') !!}
    {!! form_admin()->calendar('content', 'Content') !!}
    {!! form_admin()->time('content', 'Content') !!}
    {!! form_admin()->select('content', 'Content', ['1' => 'caca', '2' => 'maca'], '2', ['multiple' => 'multiple']) !!}
    {!! form_admin()->editor('content', 'Content') !!}

    {!! uploader()->field('image')->label('Image')->model($item)->types('image', 'video', 'audio', 'file')->manager() !!}
    {!! uploader()->field('video')->label('Video')->model($item)->types('video')->manager() !!}
    {!! uploader()->field('audio')->label('Audio')->model($item)->types('audio')->manager() !!}
    {!! uploader()->field('file')->label('File')->model($item)->types('file')->manager() !!}


    {{--{!! form_admin()->file('image') !!}
    {!! form_admin()->file('video') !!}
    {!! form_admin()->file('audio') !!}
    {!! form_admin()->file('file') !!}--}}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\TestRequest::class, '.form') !!}
@append