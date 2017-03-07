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
        {!! button()->filter() !!}
        {!! button()->clear() !!}
    </div>
    {!! form()->close() !!}
</section>