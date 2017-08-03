@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';
        var discountsTab = $('div.tab-discounts');
        var discountsContainer = $('div.discounts-container');

        var listDiscounts = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.products.load_all_discounts') }}',
                async: false,
                data: {
                    _token: token,
                    product_id: discountsContainer.data('product-id'),
                    draft: discountsContainer.data('draft'),
                    revision: discountsContainer.data('revision'),
                    disabled: discountsContainer.data('disabled')
                },
                beforeSend: function () {
                    discountsContainer.hide();
                    discountsTab.find('.loading').fadeIn(300);
                },
                complete: function () {
                    discountsTab.find('.loading').fadeOut(300);

                    setTimeout(function () {
                        discountsContainer.fadeIn(300);
                    }, 300);
                },
                success : function(data) {
                    if (data.status == true) {
                        discountsContainer.html(data.html);

                        initDiscountSelect();
                        orderDiscounts();
                    } else {
                        discountsTab.hide();
                        init.FlashMessage('error', 'Could not load the discounts! Please try again.');
                    }
                },
                error: function (err) {
                    discountsTab.hide();
                    init.FlashMessage('error', 'Could not load the discounts! Please try again.');
                }
            });
        }, assignDiscount = function (_this) {
            var container = _this.closest('.discounts-container');
            var table = container.find('.discounts-table');
            var select = container.find('.discount-assign-select');

            if (select.val()) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.products.load_one_discount') }}',
                    data: {
                        _token: token,
                        discount_id: select.val()
                    },
                    beforeSend: function () {
                        container.css({opacity: 0.5});
                    },
                    complete: function () {
                        container.css({opacity: 1});
                    },
                    success : function(data) {
                        if (data.status == true) {
                            table.find('tr.no-discounts-assigned').remove();
                            table.find('tbody').append(
                                $('#discount-row-template').html()
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
            var container = _this.closest('.discounts-container');
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
                        $('#no-discount-row-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderDiscounts();
            }, 250);
        }, orderDiscounts = function () {
            $(".discounts-table").tableDnD({
                onDrop: function(table, row){
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="discounts[' + $(selector).attr('data-index') + '][' + $(selector).attr('data-discount-id') + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, getLastDiscountIndex = function () {
            var inputs = $('.discounts-request').find('input.discount-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initDiscountSelect = function () {
            $('.discount-assign-select').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            listDiscounts();

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