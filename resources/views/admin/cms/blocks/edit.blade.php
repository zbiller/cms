@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.blocks._tabs')
    </section>

    <section class="view">
        @include('admin.cms.blocks._form', ['url' => route('admin.blocks.update', ['id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.cms.blocks._buttons')
@endsection