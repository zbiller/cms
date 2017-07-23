@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.menus._tabs')
    </section>

    <section class="view">
        @include('admin.cms.menus._form', ['url' => route('admin.menus.store', ['location' => $location, 'parent' => $parent ?: null])])
    </section>
@endsection

@section('footer')
    @include('admin.cms.menus._buttons')
@endsection