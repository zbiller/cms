@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.blocks._tabs', ['on_draft' => true])
    </section>

    <section class="view">
        @include('admin.cms.blocks._form', ['on_draft' => true])
    </section>
@endsection

{!! draft()->view($draft, $item) !!}