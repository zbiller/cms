@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.attributes.sets._tabs')
    </section>

    <section class="view">
        @include('admin.shop.attributes.sets._form', ['url' => route('admin.attribute_sets.store')])
    </section>
@endsection

@section('footer')
    @include('admin.shop.attributes.sets._buttons')
@endsection