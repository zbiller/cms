@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';
        var taxesTab = $('div.tab-taxes');
        var taxesContainer = $('div.taxes-container');

        var listTaxes = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.products.load_all_taxes') }}',
                async: false,
                data: {
                    _token: token,
                    product_id: taxesContainer.data('product-id'),
                    draft: taxesContainer.data('draft'),
                    revision: taxesContainer.data('revision'),
                    disabled: taxesContainer.data('disabled')
                },
                beforeSend: function () {
                    taxesContainer.hide();
                    taxesTab.find('.loading').fadeIn(300);
                },
                complete: function () {
                    taxesTab.find('.loading').fadeOut(300);

                    setTimeout(function () {
                        taxesContainer.fadeIn(300);
                    }, 300);
                },
                success : function(data) {
                    if (data.status == true) {
                        taxesContainer.html(data.html);

                        initTaxSelect();
                        orderTaxes();
                    } else {
                        taxesTab.hide();
                        init.FlashMessage('error', 'Could not load the taxes! Please try again.');
                    }
                },
                error: function (err) {
                    taxesTab.hide();
                    init.FlashMessage('error', 'Could not load the taxes! Please try again.');
                }
            });
        }, assignTax = function (_this) {
            var container = _this.closest('.taxes-container');
            var table = container.find('.taxes-table');
            var select = container.find('.tax-assign-select');

            if (select.val()) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.products.load_one_tax') }}',
                    data: {
                        _token: token,
                        tax_id: select.val()
                    },
                    beforeSend: function () {
                        container.css({opacity: 0.5});
                    },
                    complete: function () {
                        container.css({opacity: 1});
                    },
                    success : function(data) {
                        if (data.status == true) {
                            table.find('tr.no-taxes-assigned').remove();
                            table.find('tbody').append(
                                $('#tax-row-template').html()
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
            var container = _this.closest('.taxes-container');
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
                        $('#no-tax-row-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderTaxes();
            }, 250);
        }, orderTaxes = function () {
            $(".taxes-table").tableDnD({
                onDrop: function(table, row){
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="taxes[' + $(selector).attr('data-index') + '][' + $(selector).attr('data-tax-id') + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, getLastTaxIndex = function () {
            var inputs = $('.taxes-request').find('input.tax-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initTaxSelect = function () {
            $('.tax-assign-select').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            listTaxes();

            $(document).on('click', 'a.tax-assign', function (e) {
                e.preventDefault();

                assignTax($(this));
            });

            $(document).on('click', 'a.tax-remove:not(.disabled)', function (e) {
                e.preventDefault();

                removeTax($(this));
            });
        });
    </script>
@append