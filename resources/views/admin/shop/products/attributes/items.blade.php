<table class="attributes-table" cellspacing="0" cellpadding="0" border="0">
    @include('admin.shop.products.attributes.table')
</table>

@if($disabled === false)
    <div class="attribute-assign-container">
        <div class="attribute-assign-select-container">
            <select class="attribute-assign-select assign-attribute-set">
                <option value="" selected="selected" disabled>Set</option>
                @foreach($sets as $set)
                    <option value="{{ $set->id }}">{{ $set->name }}</option>
                @endforeach
            </select>
            <select class="attribute-assign-select assign-attribute-id">
                <option value="" selected="selected" disabled>Attribute</option>
            </select>
            <select class="attribute-assign-select assign-value-id">
                <option value="" selected="selected" disabled>Value</option>
            </select>
            <textarea class="attribute-value-assign-textarea assign-attribute-value" placeholder="Custom Value"></textarea>
        </div>
        <div class="attribute-assign-btn-container">
            <a href="#" class="attribute-assign btn green no-margin right">
                <i class="fa fa-plus"></i>&nbsp; Assign
            </a>
        </div>
    </div>
@endif

<div class="attributes-request">
    @php($attributes = $product->attributes)
    @if($attributes->count() > 0)
        @foreach($attributes as $index => $attribute)
            @php($pivot = $attribute->pivot)
            {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . ']', $pivot->product_id, ['class' => 'attribute-input', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . '][value_id]', $pivot->value_id, ['class' => 'attribute-input', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . '][value]', $pivot->value, ['class' => 'attribute-input attribute-custom-value', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . '][ord]', $pivot->ord, ['class' => 'attribute-input', 'data-index' => $pivot->id]) !!}
        @endforeach
    @endif
</div>

@php($product->clearQueryCache())