<table class="taxes-table" cellspacing="0" cellpadding="0" border="0">
    @include('admin.shop.taxes.assign.table')
</table>

@if($disabled === false)
    <div class="tax-assign-container">
        <div class="tax-assign-select-container">
            <select class="tax-assign-select">
                <option value="" selected="selected"></option>
                @foreach($taxes as $tax)
                    <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="tax-assign-btn-container">
            <a href="#" class="tax-assign btn green no-margin right">
                <i class="fa fa-plus"></i>&nbsp; Assign
            </a>
        </div>
    </div>
@endif

<div class="taxes-request">
    @php($taxes = $product->taxes)
    @if($taxes->count() > 0)
        @foreach($taxes as $index => $tax)
            @php($pivot = $tax->pivot)
            {!! form()->hidden('taxes[' . $pivot->id . '][' . $tax->id . ']', $pivot->product_id, ['class' => 'tax-input', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('taxes[' . $pivot->id . '][' . $tax->id . '][ord]', $pivot->ord, ['class' => 'tax-input', 'data-index' => $pivot->id]) !!}
        @endforeach
    @endif
</div>

@php($product->clearQueryCache())