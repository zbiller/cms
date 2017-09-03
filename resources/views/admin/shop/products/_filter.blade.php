{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->query('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        <select name="category">
            <option value="" selected="selected">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $category->id == request()->query('category') ? 'selected="selected"' : '' }}>
                    {{ str_repeat('&nbsp;', $category->depth * 4) . $category->name }}
                </option>
            @endforeach
        </select>
    </fieldset>
    <fieldset>
        {!! form()->select('active', ['' => 'Active'] + $actives, request()->query('active') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('price[0]', request()->query('price')[0] ?: null, ['placeholder' => 'Price From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('price[1]', request()->query('price')[1] ?: null, ['placeholder' => 'Price To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('quantity[0]', request()->query('quantity')[0] ?: null, ['placeholder' => 'Quant. From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('quantity[1]', request()->query('quantity')[1] ?: null, ['placeholder' => 'Quant. To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form_admin()->calendar('start_date', false, request()->query('start_date') !== null ? request()->query('start_date') : null, ['placeholder' => 'Date From', 'style' => 'width: 48%;']) !!}
        {!! form_admin()->calendar('end_date', false, request()->query('end_date') !== null ? request()->query('end_date') : null, ['placeholder' => 'Date To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <div>
        {!! button()->filterRecords() !!}
        {!! button()->clearFilters() !!}
    </div>
{!! form()->close() !!}