{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('anchor') !!}
</div>
<div id="tab-2" class="tab">
    @include('blocks_' . ($item->exists ? $item->type : $type) . '::admin')
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\BlockRequest::class, '.form') !!}

    <script type="text/javascript">
        $(function () {

        });
    </script>
@append