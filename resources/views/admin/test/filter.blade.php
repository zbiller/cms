<section class="filters">
    {!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        <select name="type" placeholder="Type">
            <option value="">Select</option>
            @for($i=1; $i<=5; $i++)
                <option value="{{ $i }}" {!! request()->get('type') == $i ? 'selected' : '' !!}>Type {{ $i }}</option>
            @endfor
        </select>
    </fieldset>
    <div>
        <a class="btn blue filter no-margin-top no-margin-bottom no-margin-left">
            <i class="fa fa-filter"></i>&nbsp; Filter
        </a>
        <a class="btn gray clear no-margin-top no-margin-bottom no-margin-right">
            <i class="fa fa-ban"></i>&nbsp; Clear
        </a>
    </div>
    {!! form()->close() !!}
</section>