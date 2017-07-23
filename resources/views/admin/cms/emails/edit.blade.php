@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.emails._tabs')
    </section>

    <section class="view">
        @include('admin.cms.emails._form', ['url' => route('admin.emails.update', ['id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.cms.emails._buttons')
@endsection