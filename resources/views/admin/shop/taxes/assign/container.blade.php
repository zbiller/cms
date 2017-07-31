<div class="loading loading-taxes">
    <img src="{{ asset('/build/assets/img/admin/loading.gif') }}" />
</div>
<div class="taxes-container"
     data-product-id="{{ $item->id }}"
     data-draft="{{ $draft ? $draft->id : null }}"
     data-revision="{{ $revision ? $revision->id : null }}"
     data-disabled="{{ $disabled }}"
></div>

<script type="x-template" id="tax-row-template">
    <tr id="#index#" data-tax-id="#tax_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
        <td>#tax_name#</td>
        <td>#tax_rate#</td>
        <td>#tax_type#</td>
        <td>
            <a href="#tax_url#" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="tax-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-tax-row-template">
    <tr class="no-taxes-assigned nodrag nodrop">
        <td colspan="10">
            There are no taxes assigned to this product
        </td>
    </tr>
</script>
<script type="x-template" id="tax-request-template">
    {!! form()->hidden('taxes[#index#][#tax_id#]', '#tax_id#', ['class' => 'tax-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('taxes[#index#][#tax_id#][ord]', '#tax_ord#', ['class' => 'tax-input', 'data-index' => '#index#']) !!}
</script>

@include('admin.shop.taxes.assign.scripts')