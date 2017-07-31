<table class="discounts-table" cellspacing="0" cellpadding="0" border="0">
    @include('admin.shop.discounts.assign.table')
</table>

@if($disabled === false)
    <div class="discount-assign-container">
        <div class="discount-assign-select-container">
            <select class="discount-assign-select">
                <option value="" selected="selected"></option>
                @foreach($discounts as $discount)
                    <option value="{{ $discount->id }}">{{ $discount->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="discount-assign-btn-container">
            <a href="#" class="discount-assign btn green no-margin right">
                <i class="fa fa-plus"></i>&nbsp; Assign
            </a>
        </div>
    </div>
@endif

<div class="discounts-request">
    @php($discounts = $product->discounts)
    @if($discounts->count() > 0)
        @foreach($discounts as $index => $discount)
            @php($pivot = $discount->pivot)
            {!! form()->hidden('discounts[' . $pivot->id . '][' . $discount->id . ']', $pivot->product_id, ['class' => 'discount-input', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('discounts[' . $pivot->id . '][' . $discount->id . '][ord]', $pivot->ord, ['class' => 'discount-input', 'data-index' => $pivot->id]) !!}
        @endforeach
    @endif
</div>

@php($product->clearQueryCache())