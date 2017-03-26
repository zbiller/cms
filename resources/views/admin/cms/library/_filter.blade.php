{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('type', $types, request('type') ?: null, ['placeholder' => 'All Types']) !!}
    </fieldset>
    <fieldset>
        {!! form()->text('size[]', request('size')[0] ?: null, ['placeholder' => 'Size From', 'style' => 'width: 45%;']) !!}
        {!! form()->text('size[]', request('size')[1] ?: null, ['placeholder' => 'Size To', 'style' => 'width: 45%;']) !!}
    </fieldset>
    <fieldset>
        {!! adminform()->calendar('start_date', false, request('start_date') !== null ? request('start_date') : null, ['placeholder' => 'Date From', 'style' => 'width: 45%;']) !!}
        {!! adminform()->calendar('end_date', false, request('end_date') !== null ? request('end_date') : null, ['placeholder' => 'Date To', 'style' => 'width: 45%;']) !!}
    </fieldset>
    <div>
        {!! button()->filter() !!}
        {!! button()->clear() !!}
    </div>
{!! form()->close() !!}