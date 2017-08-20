@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.attribute_sets.edit', $set) }}" class="real-tab">Back To Set</a>
        <a href="{{ route('admin.attributes.edit', ['set' => $set, 'attribute' => $attribute]) }}" class="real-tab">Back To Attribute</a>
    </section>

    <section class="tabs">
        @include('admin.shop.attributes.values._tabs')
    </section>

    <section class="view">
        @include('admin.shop.attributes.values._form', ['url' => route('admin.attribute_values.store', ['set' => $set, 'attribute' => $attribute])])
    </section>
@endsection

@section('footer')
    @include('admin.shop.attributes.values._buttons')
@endsection