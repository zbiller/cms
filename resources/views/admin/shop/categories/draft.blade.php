@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.categories._tabs', ['on_draft' => true])
    </section>

    <section class="view">
        @include('admin.shop.categories._form', ['on_draft' => true])
    </section>
@endsection

{!! draft()->view($draft, $item) !!}