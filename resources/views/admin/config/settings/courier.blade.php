@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="#tab-1">Basic</a>
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => url()->current(), 'method' => 'POST', 'class' => 'form']) !!}
        {!! validation('admin')->errors() !!}

        <div id="tab-1" class="tab">
            {!! form_admin()->number('transport-price', 'Transport Price (' . config('shop.price.default_currency') . ')', setting()->value('transport-price')) !!}
            {!! form_admin()->number('transport-threshold', 'Transport Threshold (' . config('shop.price.default_currency') . ')', setting()->value('transport-threshold')) !!}
        </div>
        {!! form_admin()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord() !!}
    </section>
@endsection