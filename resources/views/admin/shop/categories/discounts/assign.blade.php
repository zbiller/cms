@php($categoryDiscounts = $item->discounts()->get())

{!! form()->hidden('touch_discounts', true) !!}

<div class="box danger" style="margin-bottom: 20px;">
    <span>
        Please note that the applied discounts from a category are inherited by default by every product belonging to that category.
    </span>
</div>
<div class="box warning" style="margin-bottom: 20px;">
    <span>
        Please note that when applying multiple discounts, the product's final price will lower progressively, applying the discounts in cascade in the order they are assigned.
    </span>
</div>
<div class="discounts-container">
    <table class="assign-table discounts-table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr class="even nodrag nodrop">
                <td>Name</td>
                <td>Rate</td>
                <td>Type</td>
                <td class="actions-assign">Actions</td>
            </tr>
        </thead>
        <tbody>
        @if($categoryDiscounts->count())
            @foreach($categoryDiscounts as $discount)
                <tr id="{{ $discount->pivot->id }}" data-discount-id="{{ $discount->id }}" data-index="{{ $discount->pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                    <td>{{ $discount->name ?: 'N/A' }}</td>
                    <td>{{ $discount->rate ?: 'N/A' }}</td>
                    <td>{{ $discountTypes[$discount->type] ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                            <i class="fa fa-eye"></i>&nbsp; View
                        </a>
                        <a href="#" class="discount-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                            <i class="fa fa-times"></i>&nbsp; Remove
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="no-discounts-assigned no-assignments nodrag nodrop">
                <td colspan="10">
                    There are no discounts assigned to this category
                </td>
            </tr>
        @endif
        </tbody>
    </table>

    @if($disabled === false)
        <div class="assign-container">
            <div class="assign-content">
                <select class="assign-discount assign-select">
                    <option value="" selected="selected"></option>
                    @foreach($discounts as $discount)
                        <option value="{{ $discount->id }}">{{ $discount->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="assign-footer">
                <a href="#" class="discount-assign btn green no-margin right">
                    <i class="fa fa-plus"></i>&nbsp; Assign
                </a>
            </div>
        </div>
    @endif

    <div class="discounts-request">
        @if($categoryDiscounts->count() > 0)
            @foreach($categoryDiscounts as $index => $discount)
                @php($pivot = $discount->pivot)
                {!! form()->hidden('discounts[' . $pivot->id . '][' . $discount->id . ']', $pivot->category_id, ['class' => 'discount-input', 'data-index' => $pivot->id]) !!}
                {!! form()->hidden('discounts[' . $pivot->id . '][' . $discount->id . '][ord]', $pivot->ord, ['class' => 'discount-input', 'data-index' => $pivot->id]) !!}
            @endforeach
        @endif
    </div>
</div>

<script type="x-template" id="one-discount-template">
    <tr id="#index#" data-discount-id="#discount_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
        <td>#discount_name#</td>
        <td>#discount_rate#</td>
        <td>#discount_type#</td>
        <td>
            <a href="#discount_url#" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="discount-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-discounts-template">
    <tr class="no-discounts-assigned nodrag nodrop">
        <td colspan="10">
            There are no discounts assigned to this category
        </td>
    </tr>
</script>
<script type="x-template" id="discount-request-template">
    {!! form()->hidden('discounts[#index#][#discount_id#]', '#discount_id#', ['class' => 'discount-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('discounts[#index#][#discount_id#][ord]', '#discount_ord#', ['class' => 'discount-input', 'data-index' => '#index#']) !!}
</script>

@php($item->clearQueryCache())

@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';
        var discountsTab = $('div.tab-discounts');
        var discountsContainer = $('div.discounts-container');

        var assignDiscount = function (_this) {
            var container = _this.closest('div.discounts-container');
            var table = container.find('table.discounts-table');
            var select = container.find('select.assign-discount');

            if (select.val()) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.categories.load_discount') }}',
                    data: {
                        _token: token,
                        discount_id: select.val()
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
                            table.find('tr.no-discounts-assigned').remove();

                            table.find('tbody').append(
                                $('#one-discount-template').html()
                                    .replace(/#index#/g, parseInt(getLastDiscountIndex()) + 1)
                                    .replace(/#discount_id#/g, data.data.id)
                                    .replace(/#discount_name#/g, data.data.name)
                                    .replace(/#discount_rate#/g, data.data.rate)
                                    .replace(/#discount_type#/g, data.data.type)
                                    .replace(/#discount_url#/g, data.data.url)
                            );

                            $('.discounts-request').append(
                                $('#discount-request-template').html()
                                    .replace(/#index#/g, parseInt(getLastDiscountIndex()) + 1)
                                    .replace(/#discount_id#/g, data.data.id)
                                    .replace(/#discount_ord#/g, table.find('tbody > tr').length)
                            );

                            orderDiscounts();
                        } else {
                            init.FlashMessage('error', 'Could not assign the discount! Please try again.');
                        }
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not assign the discount! Please try again.');
                    }
                });
            }
        }, removeDiscount = function (_this) {
            var container = _this.closest('div.discounts-container');
            var table = _this.closest('table');
            var row = _this.closest('tr');
            var input = $('input.discount-input[data-index="' + row.data('index') + '"]');

            container.css({
                opacity: 0.5
            });

            setTimeout(function () {
                var count = table.find('tbody > tr').length;

                input.remove();
                row.remove();

                if (count <= 1) {
                    table.find('tbody').append(
                        $('#no-discounts-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderDiscounts();
            }, 250);
        }, orderDiscounts = function () {
            $("table.discounts-table").tableDnD({
                onDrop: function(table, row){
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="discounts[' + $(selector).attr('data-index') + '][' + $(selector).attr('data-discount-id') + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, getLastDiscountIndex = function () {
            var inputs = $('div.discounts-request').find('input.discount-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initDiscountSelect = function () {
            $('select.assign-discount').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            initDiscountSelect();
            orderDiscounts();

            $(document).on('click', 'a.discount-assign', function (e) {
                e.preventDefault();

                assignDiscount($(this));
            });

            $(document).on('click', 'a.discount-remove:not(.disabled)', function (e) {
                e.preventDefault();

                removeDiscount($(this));
            });
        });
    </script>
@append