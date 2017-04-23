{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('type', $types, request('type') ?: null, ['placeholder' => 'All Types']) !!}
    </fieldset>
    <div>
        {!! button()->filter() !!}
        {!! button()->clear() !!}
    </div>
{!! form()->close() !!}