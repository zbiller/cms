@extends('layouts::admin.default')

@section('content')
    <section class="content">
        <div class="box warning">
        <span>
            <em>IMPORTANT!</em>&nbsp;
            Please note that when creating / updating the sitemap files, the job will be queued to run in the background, so you won't immediately see any changes.
        </span>
        </div>
    </section>

    <section class="content content-third one">
        {!! form()->open(['url' => route('admin.sitemap.generate'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-sitemap"></i>&nbsp; Create Or Update Sitemap Files', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to generate a new sitemap.xml file? The old file, if exists, will be replaced.")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-third two">
        {!! form()->open(['url' => route('admin.sitemap.download'), 'method' => 'GET']) !!}
        {!! form()->button('<i class="fa fa-download"></i>&nbsp; Download All Sitemap Files', ['type' => 'submit', 'class' => 'btn green full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to download all sitemap xml files?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-third three">
        {!! form()->open(['url' => route('admin.sitemap.clear'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Remove All Sitemap Files', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to generate a new sitemap.xml file? The old file, if exists, will be replaced.")']) !!}
        {!! form()->close() !!}
    </section>

    @include('admin.seo.sitemap._list_main')
    @include('admin.seo.sitemap._list_crawl')
    @include('admin.seo.sitemap._list_database')
@endsection

