@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.currencies._tabs')
    </section>

    <section class="view">
        @include('admin.shop.currencies._form', ['url' => route('admin.currencies.update', ['id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.shop.currencies._buttons')
@endsection

