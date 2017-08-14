{!! form()->hidden('touch_items', true) !!}

<div class="items-container">
    <table class="assign-table items-table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr class="even">
                <td>Name</td>
                <td>Quantity</td>
                <td>Price</td>
                <td>Total</td>
                <td class="actions-assign">Actions</td>
            </tr>
        </thead>
        <tbody>
            @if($items->count())
                @foreach($items as $item)
                    @php($product = $item->product)
                    <tr data-product-id="{{ $product->id }}" data-index="{{ $item->id }}">
                        <td>{{ $item->name ?: 'N/A' }}</td>
                        <td>{!! form()->number('', $item->quantity ?: 1, ['class' => 'assign-item-quantity-update assign-input-update']) !!}</td>
                        <td>{{ $item->grand_price ? number_format($item->grand_price, 2) . ' ' . $item->currency : 'N/A' }}</td>
                        <td>{{ $item->grand_total ? number_format($item->grand_total, 2) . ' ' . $item->currency : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $item->product_id) }}" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                                <i class="fa fa-eye"></i>&nbsp; View
                            </a>
                            <a href="#" class="item-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                                <i class="fa fa-times"></i>&nbsp; Remove
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="no-items-assigned no-assignments nodrag nodrop">
                    <td colspan="10">
                        There are no items assigned to this order
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($disabled === false)
        <div class="assign-container">
            <div class="assign-content full">
                <select class="assign-item assign-select full">
                    <option selected="selected" disabled></option>
                </select>
                <input type="text" class="assign-input assign-item-quantity" placeholder="Quantity" />
            </div>
            <div class="assign-footer full">
                <a href="#" class="item-add btn green no-margin right">
                    <i class="fa fa-plus"></i>&nbsp; Add
                </a>
            </div>
        </div>
    @endif

    <div class="items-request">
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                {!! form()->hidden('items[' . $item->id . '][product_id]', $item->product_id, ['class' => 'item-input', 'data-index' => $item->id]) !!}
                {!! form()->hidden('items[' . $item->id . '][quantity]', $item->quantity, ['class' => 'item-quantity-change item-input', 'data-index' => $item->id]) !!}
            @endforeach
        @endif
    </div>
</div>

<script type="x-template" id="one-item-template">
    <tr data-product-id="#product_id#" data-index="#index#">
        <td>#name#</td>
        <td>{!! form()->number('', '#quantity#', ['class' => 'assign-item-quantity-update assign-input-update']) !!}</td>
        <td>#price_formatted# #currency#</td>
        <td>#total_formatted# #currency#</td>
        <td>
            <a href="#product_url#" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="item-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-items-template">
    <tr class="no-items-assigned nodrag nodrop">
        <td colspan="10">
            There are no items assigned to this order
        </td>
    </tr>
</script>
<script type="x-template" id="item-request-template">
    {!! form()->hidden('items[#index#][product_id]', '#product_id#', ['class' => 'item-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('items[#index#][quantity]', '#quantity#', ['class' => 'item-quantity-change item-input', 'data-index' => '#index#']) !!}
</script>

@php($item->clearQueryCache())

@section('bottom_scripts')
    <script type="text/javascript">
        var timer;
        var token = '{{ csrf_token() }}';

        var addItem = function (_this) {
            var container = _this.closest('div.items-container');
            var table = container.find('table.items-table');
            var product_id = container.find('select.assign-item').val();
            var quantity = container.find('input.assign-item-quantity').val();

            if (product_id && quantity) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.orders.load_item') }}',
                    data: {
                        _token: token,
                        product_id: product_id,
                        quantity: quantity
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
                            table.find('tr.no-items-assigned').remove();

                            table.find('tbody').append(
                                $('#one-item-template').html()
                                    .replace(/#index#/g, parseInt(getLastItemIndex()) + 1)
                                    .replace(/#product_id#/g, data.data.id)
                                    .replace(/#name#/g, data.data.name)
                                    .replace(/#currency#/g, data.data.currency)
                                    .replace(/#quantity#/g, data.data.quantity)
                                    .replace(/#price_formatted#/g, data.data.price_formatted)
                                    .replace(/#total_formatted#/g, data.data.total_formatted)
                                    .replace(/#product_url#/g, data.data.product_url)
                            );

                            $('.items-request').append(
                                $('#item-request-template').html()
                                    .replace(/#index#/g, parseInt(getLastItemIndex()) + 1)
                                    .replace(/#product_id#/g, data.data.id)
                                    .replace(/#name#/g, data.data.name)
                                    .replace(/#currency#/g, data.data.currency)
                                    .replace(/#quantity#/g, data.data.quantity)
                                    .replace(/#raw_price#/g, data.data.raw_price)
                                    .replace(/#sub_price#/g, data.data.sub_price)
                                    .replace(/#grand_price#/g, data.data.grand_price)
                                    .replace(/#raw_total#/g, data.data.raw_total)
                                    .replace(/#sub_total#/g, data.data.sub_total)
                                    .replace(/#grand_total#/g, data.data.grand_total)
                            );

                            container.find('input.assign-item-quantity').val('');
                        } else {
                            init.FlashMessage('error', 'Could not add the item! Please try again.');
                        }
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not add the item! Please try again.');
                    }
                });
            }
        }, removeItem = function (_this) {
            var container = _this.closest('div.items-container');
            var table = _this.closest('table');
            var row = _this.closest('tr');
            var input = $('input.item-input[data-index="' + row.data('index') + '"]');

            container.css({
                opacity: 0.5
            });

            setTimeout(function () {
                var count = table.find('tbody > tr').length;

                input.remove();
                row.remove();

                if (count <= 1) {
                    table.find('tbody').append(
                        $('#no-items-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });
            }, 250);
        }, updateItemQuantity = function (_this) {
            var index = _this.closest('tr').attr('data-index');
            var value = _this.val();

            console.log(index, value);

            $('div.items-request').find('input.item-quantity-change[data-index="' + index + '"]').val(value);
        }, getLastItemIndex = function () {
            var inputs = $('div.items-request').find('input.item-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initItemSelect = function () {
            $('select.assign-item').chosen({
                width: '100%',
                inherit_select_classes: true,
                placeholder_text_single: 'Start typing to search for products'
            });
        }, populateItemSelect = function () {
            $('.chosen-search input').autocomplete({
                minLength: 2,
                source: function (request, response) {
                    $.ajax({
                        url: '{{ route('admin.products.search') }}' + '?query=' + request.term,
                        dataType: "json",
                        beforeSend: function () {
                            $('ul.chosen-results').empty();
                            $('select.assign-item').empty();
                        }
                    }).done(function (data) {
                        response($.map(data, function (item) {
                            $('select.assign-item').append('<option value="' + item.id + '"' + (item.disabled == true ? 'disabled="disabled"' : '') + '>' + item.name + '</option>');
                        }));

                        $("select.assign-item").trigger("chosen:updated");
                        $('.chosen-search input').val(request.term);
                    });
                }
            });
        };

        $(function () {
            initItemSelect();

            $(document).on('keyup', '.chosen-container.assign-item .chosen-search > input', function (e) {
                populateItemSelect();
            });

            $(document).on('click', 'a.item-add', function (e) {
                e.preventDefault();

                addItem($(this));
            });

            $(document).on('click', 'a.item-remove:not(.disabled)', function (e) {
                e.preventDefault();

                removeItem($(this));
            });

            $(document).on('keyup', 'input.assign-item-quantity-update', function (e) {
                updateItemQuantity($(this));
            });
        });
    </script>
@append