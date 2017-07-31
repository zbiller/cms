<thead>
    <tr class="even nodrag nodrop">
        <td>Name</td>
        <td>Rate</td>
        <td>Type</td>
        <td class="actions-discounts">Actions</td>
    </tr>
</thead>
<tbody>
    @php($discounts = $product->discounts)
    @if($discounts->count())
        @foreach($discounts as $discount)
            <tr id="{{ $discount->pivot->id }}" data-discount-id="{{ $discount->id }}" data-index="{{ $discount->pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                <td>{{ $discount->name ?: 'N/A' }}</td>
                <td>{{ $discount->rate ?: 'N/A' }}</td>
                <td>{{ $discount->type ?: 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                        <i class="fa fa-eye"></i>&nbsp; View
                    </a>
                    <a href="#" class="discount-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                        <i class="fa fa-times"></i>&nbsp; Remove
                    </a>
                </td>
            </tr>
        @endforeach
    @else
        <tr class="no-discounts-assigned nodrag nodrop">
            <td colspan="10">
                There are no discounts assigned to this product
            </td>
        </tr>
    @endif
</tbody>