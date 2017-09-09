@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.discounts._tabs')
    </section>

    <section class="view">
        @include('admin.shop.discounts._form', ['url' => route('admin.discounts.update', $item->id)])
    </section>
@endsection

@section('footer')
    @include('admin.shop.discounts._buttons')
@endsection

