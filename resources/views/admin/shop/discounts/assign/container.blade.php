<div class="loading loading-discounts">
    <img src="{{ asset('/build/assets/img/admin/loading.gif') }}" />
</div>
<div class="discounts-container"
     data-product-id="{{ $item->id }}"
     data-draft="{{ $draft ? $draft->id : null }}"
     data-revision="{{ $revision ? $revision->id : null }}"
     data-disabled="{{ $disabled }}"
></div>

<script type="x-template" id="discount-row-template">
    <tr id="#index#" data-discount-id="#discount_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
        <td>#discount_name#</td>
        <td>#discount_rate#</td>
        <td>#discount_type#</td>
        <td>
            <a href="#discount_url#" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="discount-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-discount-row-template">
    <tr class="no-discounts-assigned nodrag nodrop">
        <td colspan="10">
            There are no discounts assigned to this product
        </td>
    </tr>
</script>
<script type="x-template" id="discount-request-template">
    {!! form()->hidden('discounts[#index#][#discount_id#]', '#discount_id#', ['class' => 'discount-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('discounts[#index#][#discount_id#][ord]', '#discount_ord#', ['class' => 'discount-input', 'data-index' => '#index#']) !!}
</script>

@include('admin.shop.discounts.assign.scripts')