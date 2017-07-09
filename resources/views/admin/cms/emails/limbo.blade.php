@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
        <a href="#tab-2">Manage Details</a>
        <a href="#tab-3">Variables</a>
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['method' => 'PUT', 'class' => 'form']) !!}

        {!! form()->hidden('_back', route('admin.emails.drafts')) !!}
        {!! form()->hidden('_class', \App\Models\Cms\Email::class) !!}
        {!! form()->hidden('_id', $item->exists ? $item->id : null) !!}

        <div id="tab-1" class="tab">
            {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
            {!! form_admin()->text('name') !!}
            {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier') : '' !!}
        </div>
        <div id="tab-2" class="tab">
            <div class="box notification" style="margin-bottom: 20px;">
                <span>
                    Please note that in order for the email to work properly, you need to include the defined variables into the "Message" section.<br />
                    The variables can be found on the "Variables" tab and should be typed into the message editor with the following syntax --- [variable_name]<br />
                    Although recommended, including all variables is not required.<br />
                </span>
            </div>

            {!! form_admin()->text('metadata[from_name]', 'From Name', $item && $item->exists ? $item->metadata('from_name') : null, ['placeholder' => 'Default is ' . $fromName]) !!}
            {!! form_admin()->text('metadata[from_email]', 'From Email', $item && $item->exists ? $item->metadata('from_email') : null, ['placeholder' => 'Default is ' . $fromEmail]) !!}
            {!! form_admin()->text('metadata[reply_to]', 'Reply To', $item && $item->exists ? $item->metadata('reply_to') : null, ['placeholder' => 'Default is ' . $fromEmail]) !!}
            {!! uploader()->field('metadata[attachment]')->label('Attachment')->model($item)->manager() !!}
            {!! form_admin()->text('metadata[subject]', 'Subject') !!}
            {!! form_admin()->editor('metadata[message]', 'Message') !!}
        </div>
        <div id="tab-3" class="tab">
            @foreach($variables as $variable => $attributes)
                <span class="title">{{ $attributes['name'] }}</span>
                <p><span style="font-family: 'Open Sans Bold', sans-serif;">Name</span>: {{ $variable }}</p>
                <p>{{ $attributes['label'] }}<br />{{ $attributes['description'] }}</p>
                <p>Use this variable inside the editor for the "Message" input on the "Manage Details" tab. Syntax --- [{{ $variable }}]</p>
            @endforeach
        </div>
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.emails.drafts')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}
        {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
    </section>
@endsection

