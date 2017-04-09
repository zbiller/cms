{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('layout', $layouts->pluck('name', 'id'), request('layout') ?: null, ['placeholder' => 'All Layouts']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('type', $types, request('type') ?: null, ['placeholder' => 'All Types']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('active', $actives, request('active') ?: null, ['placeholder' => 'Active']) !!}
    </fieldset>
    <fieldset>
        {!! form_admin()->calendar('start_date', false, request('start_date') !== null ? request('start_date') : null, ['placeholder' => 'Date From', 'style' => 'width: 48%;']) !!}
        {!! form_admin()->calendar('end_date', false, request('end_date') !== null ? request('end_date') : null, ['placeholder' => 'Date To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <div>
        {!! button()->filter() !!}
        {!! button()->clear() !!}
    </div>
{!! form()->close() !!}