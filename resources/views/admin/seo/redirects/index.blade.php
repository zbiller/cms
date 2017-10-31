@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.seo.redirects._filter')
    </section>

    <section class="content">
        <div class="box yellow">
            <span>
                <em>IMPORTANT!</em>&nbsp;
                In order to fully fix the broken links after the script has automatically found them, you will have to manually set the <em>NEW URL</em> for each one.
            </span>
        </div>
    </section>

    <section class="content content-third one">
        {!! form()->open(['url' => route('admin.redirects.find'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-chain-broken"></i>&nbsp; Find broken links', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to start a check for broken links in the entire application?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-third two">
        {!! form()->open(['url' => route('admin.redirects.clean'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Clean bad or empty redirects', ['type' => 'submit', 'class' => 'btn green full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all redirects that point nowhere?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-third three">
        {!! form()->open(['url' => route('admin.redirects.clear'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Remove all redirects', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to permanently remove all redirect mappings?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.seo.redirects._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.redirects.create')) !!}
    </section>
@endsection