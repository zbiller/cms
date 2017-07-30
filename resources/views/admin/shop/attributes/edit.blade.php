@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.sets.edit', $set) }}" class="real-tab">Back To Set</a>
    </section>

    <section class="tabs">
        @include('admin.shop.attributes._tabs')
    </section>

    <section class="view">
        @include('admin.shop.attributes._form', ['url' => route('admin.attributes.update', ['set' => $set->id, 'id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.shop.attributes._buttons')
@endsection

