@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.orders._tabs')
    </section>

    <section class="view">
        @include('admin.shop.orders._form', ['url' => route('admin.orders.store')])
    </section>
@endsection

@section('footer')
    @include('admin.shop.orders._buttons')
@endsection