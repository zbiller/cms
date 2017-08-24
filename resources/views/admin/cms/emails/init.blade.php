@extends('layouts::admin.default')

@section('content')
    <section class="view">
        {!! form_admin()->open(['class' => 'form']) !!}
            {!! form_admin()->select('type', 'Type', ['' => ''] + $types, null, ['id' => 'email-type', 'data-url' => route('admin.emails.create'), 'data-image' => asset('/images/admin/emails'), 'data-images' => json_encode($images)]) !!}
            <img src="" id="email-image" />
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.emails.index')) !!}
    </section>
    <section class="actions">
        {!! button()->action('Continue', '#', 'fa-arrow-right', 'blue', ['id' => 'email-continue-button']) !!}
    </section>
@endsection