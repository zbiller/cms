@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.categories._tabs')
    </section>

    <section class="view">
        @include('admin.shop.categories._form', ['url' => route('admin.categories.update', ['id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.shop.categories._buttons')
@endsection

