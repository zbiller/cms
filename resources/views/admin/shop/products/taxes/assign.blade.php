@php($productTaxes = $item->exists ? $item->taxes()->get() : collect())
@php($inheritedTaxes = $item->exists ? $item->getInheritedTaxes() : collect())

{!! form()->hidden('touch_taxes', true) !!}

<div class="box warning" style="margin-bottom: 20px;">
    <span>
        Please note that when applying multiple taxes, the product's final price will increase progressively, applying the taxes in cascade in the order they are assigned.
    </span>
</div>

<div style="width: 100%; float: left; margin-bottom: 15px;">
    {!! form_admin()->select('inherit_taxes', 'Inherit Taxes', $inherits) !!}
</div>

<div class="taxes-container">
    <table class="assign-table taxes-table" cellspacing="0" cellpadding="0" border="0">
        <thead>
        <tr class="even nodrag nodrop">
            <td>Name</td>
            <td>Rate</td>
            <td>Type</td>
            <td class="actions-assign">Actions</td>
        </tr>
        </thead>
        <tbody>
        @if($productTaxes->count())
            @foreach($productTaxes as $tax)
                <tr id="{{ $tax->pivot->id }}" data-tax-id="{{ $tax->id }}" data-index="{{ $tax->pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                    <td>{{ $tax->name ?: 'N/A' }}</td>
                    <td>{{ $tax->rate ?: 'N/A' }}</td>
                    <td>{{ $taxTypes[$tax->type] ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                            <i class="fa fa-eye"></i>&nbsp; View
                        </a>
                        <a href="#" class="tax-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                            <i class="fa fa-times"></i>&nbsp; Remove
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="no-taxes-assigned no-assignments nodrag nodrop">
                <td colspan="10">
                    @if($item->inherit_taxes == \App\Models\Shop\Product::INHERIT_YES && $inheritedTaxes && $inheritedTaxes->count() > 0)
                        <div class="block-inheritance">
                            <span>This product inherits the following taxes from it's main category tree: </span>
                            <em>{{ $inheritedTaxes->implode('name', ', ') }}.</em>
                            <span>Assigning taxes here, will overwrite the inherited taxes.</span>
                        </div>
                    @else
                        There are no taxes assigned to this product
                    @endif
                </td>
            </tr>
        @endif
        </tbody>
    </table>

    @if($disabled === false)
        <div class="assign-container">
            <div class="assign-content">
                <select class="assign-tax assign-select">
                    <option value="" selected="selected"></option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="assign-footer">
                <a href="#" class="tax-assign btn green no-margin right">
                    <i class="fa fa-plus"></i>&nbsp; Assign
                </a>
            </div>
        </div>
    @endif

    <div class="taxes-request">
        @if($productTaxes->count() > 0)
            @foreach($productTaxes as $index => $tax)
                @php($pivot = $tax->pivot)
                {!! form()->hidden('taxes[' . $tax->id . ']', $pivot->product_id, ['class' => 'tax-input', 'data-index' => $pivot->id]) !!}
                {!! form()->hidden('taxes[' . $tax->id . '][ord]', $pivot->ord, ['class' => 'tax-input', 'data-index' => $pivot->id]) !!}
            @endforeach
        @endif
    </div>
</div>

<script type="x-template" id="one-tax-template">
    <tr id="#index#" data-tax-id="#tax_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
        <td>#tax_name#</td>
        <td>#tax_rate#</td>
        <td>#tax_type#</td>
        <td>
            <a href="#tax_url#" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="tax-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-taxes-template">
    <tr class="no-taxes-assigned nodrag nodrop">
        <td colspan="10">
            @if($item->inherit_taxes == \App\Models\Shop\Product::INHERIT_YES && $inheritedTaxes && $inheritedTaxes->count() > 0)
                <div class="block-inheritance">
                    <span>This product inherits the following taxes from it's main category tree: </span>
                    <em>{{ $inheritedTaxes->implode('name', ', ') }}.</em>
                    <span>Assigning taxes here, will overwrite the inherited taxes.</span>
                </div>
            @else
                There are no taxes assigned to this product
            @endif
        </td>
    </tr>
</script>
<script type="x-template" id="inherited-taxes-template">
    @if($inheritedTaxes && $inheritedTaxes->count() > 0)
        <div class="block-inheritance">
            <span>This product inherits the following taxes from it's main category tree: </span>
            <em>{{ $inheritedTaxes->implode('name', ', ') }}.</em>
            <span>Assigning taxes here, will overwrite the inherited taxes.</span>
        </div>
    @else
        There are no taxes assigned to this product
    @endif
</script>
<script type="x-template" id="no-inherited-taxes-template">
    There are no taxes assigned to this product
</script>
<script type="x-template" id="tax-request-template">
    {!! form()->hidden('taxes[#tax_id#]', '#tax_id#', ['class' => 'tax-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('taxes[#tax_id#][ord]', '#tax_ord#', ['class' => 'tax-input', 'data-index' => '#index#']) !!}
</script>

@php($item->clearQueryCache())

@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';

        var assignTax = function (_this) {
            var container = _this.closest('.taxes-container');
            var table = container.find('table.taxes-table');
            var select = container.find('select.assign-tax');

            if (select.val()) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.products.load_tax') }}',
                    data: {
                        _token: token,
                        tax_id: select.val()
                    },
                    beforeSend: function () {
                        container.css({
                            opacity: 0.5
                        });
                    },
                    complete: function () {
                        container.css({
                            opacity: 1
                        });
                    },
                    success : function(data) {
                        if (data.status == true) {
                            table.find('tr.no-taxes-assigned').remove();

                            table.find('tbody').append(
                                $('#one-tax-template').html()
                                    .replace(/#index#/g, parseInt(getLastTaxIndex()) + 1)
                                    .replace(/#tax_id#/g, data.data.id)
                                    .replace(/#tax_name#/g, data.data.name)
                                    .replace(/#tax_rate#/g, data.data.rate)
                                    .replace(/#tax_type#/g, data.data.type)
                                    .replace(/#tax_url#/g, data.data.url)
                            );

                            $('.taxes-request').append(
                                $('#tax-request-template').html()
                                    .replace(/#index#/g, parseInt(getLastTaxIndex()) + 1)
                                    .replace(/#tax_id#/g, data.data.id)
                                    .replace(/#tax_ord#/g, table.find('tbody > tr').length)
                            );

                            orderTaxes();
                        } else {
                            init.FlashMessage('error', 'Could not assign the tax! Please try again.');
                        }
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not assign the tax! Please try again.');
                    }
                });
            }
        }, removeTax = function (_this) {
            var container = _this.closest('div.taxes-container');
            var table = _this.closest('table');
            var row = _this.closest('tr');
            var input = $('input.tax-input[data-index="' + row.data('index') + '"]');

            container.css({
                opacity: 0.5
            });

            setTimeout(function () {
                var count = table.find('tbody > tr').length;

                input.remove();
                row.remove();

                if (count <= 1) {
                    table.find('tbody').append(
                        $('#no-taxes-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderTaxes();
            }, 250);
        }, orderTaxes = function () {
            $("table.taxes-table").tableDnD({
                onDrop: function(table, row){
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="taxes[' + $(selector).attr('data-tax-id') + '][ord]"][data-index="' + $(selector).attr('data-index') + '"]').val(index + 1);
                    });
                }
            });
        }, inheritTaxes = function (_this) {
            if (_this.val() == '{{ \App\Models\Shop\Product::INHERIT_YES }}') {
                $('table.taxes-table').find('tr.no-taxes-assigned').find('td').html(
                    $('#inherited-taxes-template').html()
                );
            } else {
                $('table.taxes-table').find('tr.no-taxes-assigned').find('td').html(
                    $('#no-inherited-taxes-template').html()
                );
            }
        }, getLastTaxIndex = function () {
            var inputs = $('div.taxes-request').find('input.tax-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initTaxSelect = function () {
            $('select.assign-tax').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            orderTaxes();
            initTaxSelect();

            $(document).on('click', 'a.tax-assign', function (e) {
                e.preventDefault();

                assignTax($(this));
            });

            $(document).on('click', 'a.tax-remove:not(.disabled)', function (e) {
                e.preventDefault();

                removeTax($(this));
            });

            $(document).on('change', 'select[name="inherit_taxes"]', function () {
                inheritTaxes($(this));
            });
        });
    </script>
@append