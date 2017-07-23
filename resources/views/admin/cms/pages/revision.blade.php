@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.pages._tabs', ['on_revision' => true])
    </section>

    <section class="view">
        @include('admin.cms.pages._form', ['on_revision' => true])
    </section>
@endsection

{!! revision()->view($revision, $item) !!}