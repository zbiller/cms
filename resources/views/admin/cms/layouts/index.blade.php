@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.cms.layouts._filter')
    </section>

    <section class="list">
        @include('admin.cms.layouts._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.layouts.create')) !!}
    </section>
@endsection