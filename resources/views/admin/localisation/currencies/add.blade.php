@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.currencies._tabs')
    </section>

    <section class="view">
        @include('admin.shop.currencies._form', ['url' => route('admin.currencies.store')])
    </section>
@endsection

@section('footer')
    @include('admin.shop.currencies._buttons')
@endsection