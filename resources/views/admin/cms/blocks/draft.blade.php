@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
        <a href="#tab-2">Manage Details</a>
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['class' => 'form']) !!}

        {!! validation('admin')->errors() !!}

        {!! form()->hidden('_class', \App\Models\Cms\Block::class) !!}
        {!! form()->hidden('_request', \App\Http\Requests\BlockRequest::class) !!}
        {!! form()->hidden('_id', $item->exists ? $item->id : null) !!}

        <div id="tab-1" class="tab">
            {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
            {!! form_admin()->text('name', 'Name', null, []) !!}
            {!! form_admin()->text('anchor', 'Anchor', null, []) !!}
        </div>
        <div id="tab-2" class="tab">
            @include('blocks_' . ($item->exists ? $item->type : $type) . '::admin')
        </div>
        {!! form()->close() !!}
</section>
@endsection

{!! draft()->view($draft, $item) !!}