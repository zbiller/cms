@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.categories._tabs', ['on_revision' => true])
    </section>

    <section class="view">
        @include('admin.shop.categories._form', ['on_revision' => true])
    </section>
@endsection

{!! revision()->view($revision, $item) !!}