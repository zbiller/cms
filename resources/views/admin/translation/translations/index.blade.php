@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.translation.translations._filter')
    </section>

    <section class="content">
        <div class="box yellow">
            <span>
                <em>IMPORTANT!</em>&nbsp;
                Before changing anything, first <em>import</em> your translations, to ensure you see the currently used ones.
                After you have changed any translations, <em>export</em> them to actually propagate your changes.
            </span>
        </div>
    </section>

    <section class="content content-quarter one">
        {!! form()->open(['url' => route('admin.translations.import'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-download"></i>&nbsp; Import Translations', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to import the source file translations into the database?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-quarter two">
        {!! form()->open(['url' => route('admin.translations.export'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-upload"></i>&nbsp; Export Translations', ['type' => 'submit', 'class' => 'btn green full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to export the database translations into their source files?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-quarter three">
        {!! form()->open(['url' => route('admin.translations.sync'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-refresh"></i>&nbsp; Sync Missing Translations', ['type' => 'submit', 'class' => 'btn yellow full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to synchronize missing translations into the database?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-quarter four">
        {!! form()->open(['url' => route('admin.translations.clear'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Remove All Translations', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to delete all of the translations from the database?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.translation.translations._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.translations.create')) !!}
    </section>
@endsection