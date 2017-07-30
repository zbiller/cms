@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.taxes._tabs')
    </section>

    <section class="view">
        @include('admin.shop.taxes._form', ['url' => route('admin.taxes.store')])
    </section>
@endsection

@section('footer')
    @include('admin.shop.taxes._buttons')
@endsection