@extends('layouts::' . $page->layout->blade)

@section('content')
<!--========== PAGE LAYOUT ==========-->

<!-- Header Location Blocks -->
{!! block()->holder($page, 'header') !!}
<!-- End Header Location Blocks -->

<!-- Page Details -->
<div class="bg-color-sky-light">
    <div class="content-md container" style="text-align: center;">
        @if(isset($page->metadata->title))
            <h1 style="text-align: center;">{{ $page->metadata->title }}</h1>
        @endif
        @if(isset($page->metadata->subtitle))
            <h4 style="text-align: center;">{{ $page->metadata->subtitle }}</h4>
        @endif
        @if(isset($page->metadata->content))
            {!! $page->metadata->content !!}
        @endif
    </div>
</div>
<!-- End Page Details -->

<!-- Content Location Blocks -->
{!! block()->holder($page, 'content') !!}
<!-- End Content Location Blocks -->

<!--========== END PAGE LAYOUT ==========-->
@endsection