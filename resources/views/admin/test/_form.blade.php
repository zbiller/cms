{!! validation()->errors() !!}

<div id="tab-1" class="tab">


    {{--{{ dd($item->_video->url(), $item->_video->url('some_style'), $item->_video->thumbnail(2)) }}--}}

    {{--<img src="{{ $item->_image->url('landscape') }}"  /><br /><br />
    <img src="{{ $item->_video->thumbnail(2) }}"  /><br /><br />

    <video width="320" height="240" controls>
        <source src="{{ $item->_video->url('asdas') }}" type="video/ogg">
    </video>--}}

    <br /><br /><br /><br />
    <br /><br /><br /><br />




    {!! adminform()->text('name') !!}
    {!! adminform()->file('image') !!}
    {!! adminform()->file('video') !!}
    {!! adminform()->file('audio') !!}
    {!! adminform()->file('file') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Crud\TestRequest::class, '.form') !!}
@append