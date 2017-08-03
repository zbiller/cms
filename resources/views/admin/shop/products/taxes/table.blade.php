<thead>
    <tr class="even nodrag nodrop">
        <td>Name</td>
        <td>Rate</td>
        <td>Type</td>
        <td class="actions-taxes">Actions</td>
    </tr>
</thead>
<tbody>
    @php($taxes = $product->taxes)
    @php($types = \App\Models\Shop\Tax::$types)
    @if($taxes->count())
        @foreach($taxes as $tax)
            <tr id="{{ $tax->pivot->id }}" data-tax-id="{{ $tax->id }}" data-index="{{ $tax->pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                <td>{{ $tax->name ?: 'N/A' }}</td>
                <td>{{ $tax->rate ?: 'N/A' }}</td>
                <td>{{ isset($types[$tax->type]) ? $types[$tax->type] : 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                        <i class="fa fa-eye"></i>&nbsp; View
                    </a>
                    <a href="#" class="tax-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                        <i class="fa fa-times"></i>&nbsp; Remove
                    </a>
                </td>
            </tr>
        @endforeach
    @else
        <tr class="no-taxes-assigned nodrag nodrop">
            <td colspan="10">
                There are no taxes assigned to this product
            </td>
        </tr>
    @endif
</tbody>