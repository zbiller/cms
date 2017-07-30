@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.sets._tabs')
    </section>

    <section class="view">
        @include('admin.shop.sets._form', ['url' => route('admin.sets.store')])
    </section>
@endsection

@section('footer')
    @include('admin.shop.sets._buttons')
@endsection