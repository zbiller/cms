{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('type', $types, request('type') ?: null, ['placeholder' => 'All Types']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('active', $actives, request('active') ?: null, ['placeholder' => 'Active']) !!}
    </fieldset>
    <div>
        {!! button()->filterRecords() !!}
        {!! button()->clearFilters() !!}
    </div>
{!! form()->close() !!}