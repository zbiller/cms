{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('owner', $owners->pluck('full_name', 'id'), request('owner') ?: null, ['placeholder' => 'All Owners']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('brand', $brands->pluck('name', 'id'), request('brand') ?: null, ['placeholder' => 'All Brands']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('book', $books->pluck('name', 'id'), request('book') ?: null, ['placeholder' => 'All Books']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('mechanics[]', $mechanics->pluck('name', 'id'), request('mechanics') ?: null, ['placeholder' => 'All Mechanics']) !!}
    </fieldset>
    <div>
        {!! button()->filter() !!}
        {!! button()->clear() !!}
    </div>
{!! form()->close() !!}