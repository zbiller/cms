@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.products._tabs')
    </section>

    <section class="view">
        @include('admin.shop.products._form', ['url' => route('admin.products.store')])
    </section>
@endsection

@section('footer')
    @include('admin.shop.products._buttons')
@endsection