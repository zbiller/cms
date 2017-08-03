@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.sets.edit', $set) }}" class="real-tab">Back To Set</a>
        <a href="{{ route('admin.attributes.edit', ['set' => $set, 'attribute' => $attribute]) }}" class="real-tab">Back To Attribute</a>
    </section>

    <section class="tabs">
        @include('admin.shop.values._tabs')
    </section>

    <section class="view">
        @include('admin.shop.values._form', ['url' => route('admin.values.update', ['set' => $set, 'attribute' => $attribute,'id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.shop.values._buttons')
@endsection

