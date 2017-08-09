@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.attribute_sets.edit', $set) }}" class="real-tab">Back To Set</a>
    </section>

    <section class="tabs">
        @include('admin.shop.attributes._tabs')
    </section>

    <section class="view">
        @include('admin.shop.attributes._form', ['url' => route('admin.attributes.store', $set)])
    </section>
@endsection

@section('footer')
    @include('admin.shop.attributes._buttons')
@endsection