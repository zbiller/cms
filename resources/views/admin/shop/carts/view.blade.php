@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.carts._tabs')
    </section>

    <section class="view">
        @include('admin.shop.carts._form')
    </section>
@endsection

@section('footer')
    @include('admin.shop.carts._buttons')
@endsection

