<thead>
    <tr class="even nodrag nodrop">
        <td>Name</td>
        <td>Value</td>
        <td class="actions-attributes">Actions</td>
    </tr>
</thead>
<tbody>
    @php($attributes = $product->attributes()->with('set')->get())
    @if($attributes->count())
        @foreach($attributes as $attribute)
            @php($pivot = $attribute->pivot)
            <tr id="{{ $pivot->id }}" data-attribute-id="{{ $attribute->id }}" data-value-id="{{ $pivot->value_id }}" data-index="{{ $pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                <td>{{ $attribute->name ?: 'N/A' }}</td>
                <td>
                    <textarea class="attribute-value-change">{{ $pivot->value ?: (($value = \App\Models\Shop\Value::find($pivot->value_id)) ? $value->value : '') }}</textarea>
                </td>
                <td>
                    <a href="{{ route('admin.attributes.edit', ['set' => $attribute->set, 'attribute' => $attribute]) }}" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                        <i class="fa fa-eye"></i>&nbsp; View
                    </a>
                    <a href="#" class="attribute-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                        <i class="fa fa-times"></i>&nbsp; Remove
                    </a>
                </td>
            </tr>
        @endforeach
    @else
        <tr class="no-attributes-assigned nodrag nodrop">
            <td colspan="10">
                There are no attributes assigned to this product
            </td>
        </tr>
    @endif
</tbody>