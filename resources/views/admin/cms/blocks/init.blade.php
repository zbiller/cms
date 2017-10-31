@extends('layouts::admin.default')

@section('content')
    <section class="view">
        {!! form_admin()->open(['class' => 'form']) !!}
            {!! form_admin()->select('type', 'Type', ['' => ''] + $types, null, ['id' => 'block-type', 'data-url' => route('admin.blocks.create'), 'data-image' => asset('/images/admin/blocks'), 'data-images' => json_encode($images)]) !!}
            <img src="" id="block-image" />
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="left">
        {!! button()->cancelAction(route('admin.blocks.index')) !!}
    </section>
    <section class="right">
        {!! button()->action('Continue', '#', 'fa-arrow-right', 'blue', ['id' => 'block-continue-button']) !!}
    </section>
@endsection