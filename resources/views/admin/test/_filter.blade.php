{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <div>
        {!! button()->filter() !!}
        {!! button()->clear() !!}
    </div>
{!! form()->close() !!}