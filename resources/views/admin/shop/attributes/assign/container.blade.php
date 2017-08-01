<div class="loading loading-attributes">
    <img src="{{ asset('/build/assets/img/admin/loading.gif') }}" />
</div>
<div class="attributes-container"
     data-product-id="{{ $item->id }}"
     data-draft="{{ $draft ? $draft->id : null }}"
     data-revision="{{ $revision ? $revision->id : null }}"
     data-disabled="{{ $disabled }}"
></div>

<script type="x-template" id="attribute-row-template">
    <tr id="#index#" data-attribute-id="#attribute_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
        <td>#attribute_name#</td>
        <td>
            <textarea class="attribute-value-change">#attribute_value#</textarea>
        </td>
        <td>
            <a href="#attribute_url#" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="attribute-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-attribute-row-template">
    <tr class="no-attributes-assigned nodrag nodrop">
        <td colspan="10">
            There are no attributes assigned to this product
        </td>
    </tr>
</script>
<script type="x-template" id="attribute-request-template">
    {!! form()->hidden('attributes[#index#][#attribute_id#]', '#attribute_id#', ['class' => 'attribute-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('attributes[#index#][#attribute_id#][ord]', '#attribute_ord#', ['class' => 'attribute-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('attributes[#index#][#attribute_id#][val]', '#attribute_val#', ['class' => 'attribute-input', 'data-index' => '#index#']) !!}
</script>

@include('admin.shop.attributes.assign.scripts')