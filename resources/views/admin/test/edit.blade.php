@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Test Entity</h1>
@endsection

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
    </section>

    <section class="view">
        {!! Form::model($item, ['url' => route('admin.test.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form']) !!}
            <div id="tab-1" class="tab">
                <fieldset>
                    <label>Name</label>
                    <input type="text" name="name" value="{{ $item->name }}" />
                </fieldset>
            </div>
        {!! Form::close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.test.index') !!}
    </section>
    <section class="actions">
        <a class="btn dark-blue duplicate">
            <i class="fa fa-files-o"></i>&nbsp; Duplicate
        </a>
        <a class="btn yellow preview">
            <i class="fa fa-eye"></i>&nbsp; Preview
        </a>
        <a class="btn red draft">
            <i class="fa fa-cloud"></i>&nbsp; Save as Draft
        </a>
        <a class="btn green stay">
            <i class="fa fa-map-marker"></i>&nbsp; Save & Stay
        </a>
        <a id="save" class="btn blue save no-margin-right">
            <i class="fa fa-check"></i>&nbsp; Save
        </a>
    </section>
@endsection

@section('bottom_scripts')
    <script type="text/javascript">
        $('a#save').click(function (e) {
            e.preventDefault();

            $('.form').submit();
        });
    </script>
@append

