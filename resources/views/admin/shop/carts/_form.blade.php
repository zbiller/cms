@if($item->exists)
    {!! form_admin()->model($item, ['class' => 'form']) !!}
@else
    {!! form_admin()->open(['class' => 'form']) !!}
@endif

{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('user_id', 'User', [null => 'Guest'] + $users->pluck('full_name', 'id')->toArray(), null, ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('total', 'Cart Total', number_format($item->total, 2), ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('count', 'Items Count', $item->count, ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('identifier', 'Identifier', null, ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('token', 'Token', null, ['disabled' => 'disabled']) !!}
</div>
<div id="tab-2" class="tab">
    <table cellspacing="0" cellpadding="0" border="0">
        <thead>
        <tr class="even nodrag nodrop">
            <td>Name</td>
            <td>Quantity</td>
            <td class="actions-view">Actions</td>
        </tr>
        </thead>
        <tbody>
        @if($items->count())
            @foreach($items as $_item)
                <tr>
                    <td>{{ $_item->product && $_item->product->exists ? $_item->product->name : 'N/A' }}</td>
                    <td>{{ $_item->quantity ?: 'N/A' }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $_item->product) }}" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                            <i class="fa fa-eye"></i>&nbsp; View
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="no-attributes-assigned no-assignments nodrag nodrop">
                <td colspan="10">
                    There are no products inside this cart
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

{!! form_admin()->close() !!}