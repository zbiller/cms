@section('bottom_scripts')
    <script type="text/javascript">
        var timer;
        var token = '{{ csrf_token() }}';
        var attributesTab = $('div.tab-attributes');
        var attributesContainer = $('div.attributes-container');

        var loadAttributes = function (_this) {
            var set_id = _this.val();
            var selectAttribute = $('select.assign-attribute-id');
            var selectValue = $('select.assign-value-id');

            if (set_id) {
                $.ajax({
                    type : 'GET',
                    url: '{{ route('admin.attributes.get') }}' + '/' + set_id,
                    success : function(data) {
                        selectAttribute.empty();

                        $.each(data.items, function (index, attribute) {
                            selectAttribute.append('<option value="' + attribute.id + '">' + attribute.name + '</option>');

                            if (index == 0) {
                                selectValue.empty();

                                $.each(attribute.values, function (i, value) {
                                    selectValue.append('<option value="' + value.id + '">' + value.value + '</option>');
                                });
                            }
                        });

                        selectAttribute.trigger("chosen:updated");
                        selectValue.trigger("chosen:updated");
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not load the attributes! Please try again.');
                    }
                });
            }
        },loadValues = function (_this) {
            var set_id = $('select.assign-attribute-set').val();
            var attribute_id = _this.val();
            var selectValue = $('select.assign-value-id');

            if (set_id && attribute_id) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.values.get') }}' + '/' + set_id + '/' + attribute_id,
                    success: function (data) {
                        selectValue.empty();

                        $.each(data.items, function (index, value) {
                            selectValue.append('<option value="' + value.id + '">' + value.value + '</option>');
                        });

                        selectValue.trigger("chosen:updated");
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not load the attributes! Please try again.');
                    }
                });
            }
        }, listAttributes = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.products.load_all_attributes') }}',
                async: false,
                data: {
                    _token: token,
                    product_id: attributesContainer.data('product-id'),
                    draft: attributesContainer.data('draft'),
                    revision: attributesContainer.data('revision'),
                    disabled: attributesContainer.data('disabled')
                },
                beforeSend: function () {
                    attributesContainer.hide();
                    attributesTab.find('.loading').fadeIn(300);
                },
                complete: function () {
                    attributesTab.find('.loading').fadeOut(300);

                    setTimeout(function () {
                        attributesContainer.fadeIn(300);
                    }, 300);
                },
                success : function(data) {
                    if (data.status == true) {
                        attributesContainer.html(data.html);

                        initAttributeSelect();
                        orderAttributes();
                    } else {
                        attributesTab.hide();
                        init.FlashMessage('error', 'Could not load the attributes! Please try again.');
                    }
                },
                error: function (err) {
                    attributesTab.hide();
                    init.FlashMessage('error', 'Could not load the attributes! Please try again.');
                }
            });
        }, assignAttribute = function (_this) {
            var container = _this.closest('.attributes-container');
            var table = container.find('.attributes-table');
            var set_id = container.find('.assign-attribute-set').val();
            var attribute_id = container.find('.assign-attribute-id').val();
            var value_id = container.find('.assign-value-id').val();
            var value = container.find('.assign-attribute-value').val();

            if (set_id && attribute_id) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.products.load_one_attribute') }}',
                    data: {
                        _token: token,
                        set_id: set_id,
                        attribute_id: attribute_id,
                        value_id: value_id,
                        value: value
                    },
                    beforeSend: function () {
                        container.css({opacity: 0.5});
                    },
                    complete: function () {
                        container.css({opacity: 1});
                    },
                    success : function(data) {
                        if (data.status == true) {
                            table.find('tr.no-attributes-assigned').remove();

                            table.find('tbody').append(
                                $('#attribute-row-template').html()
                                    .replace(/#index#/g, parseInt(getLastAttributeIndex()) + 1)
                                    .replace(/#attribute_id#/g, data.data.attribute_id)
                                    .replace(/#attribute_name#/g, data.data.attribute_name)
                                    .replace(/#attribute_value#/g, data.data.attribute_value)
                                    .replace(/#attribute_url#/g, data.data.url)
                                    .replace(/#value_id#/g, data.data.value_id)
                            );

                            $('.attributes-request').append(
                                $('#attribute-request-template').html()
                                    .replace(/#index#/g, parseInt(getLastAttributeIndex()) + 1)
                                    .replace(/#attribute_ord#/g, table.find('tbody > tr').length)
                                    .replace(/#attribute_id#/g, data.data.attribute_id)
                                    .replace(/#attribute_value#/g, data.data.value)
                                    .replace(/#value_id#/g, data.data.value_id)
                            );

                            orderAttributes();
                        } else {
                            init.FlashMessage('error', 'Could not assign the attribute! Please try again.');
                        }
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not assign the attribute! Please try again.');
                    }
                });
            }
        }, removeAttribute = function (_this) {
            var container = _this.closest('.attributes-container');
            var table = _this.closest('table');
            var row = _this.closest('tr');
            var input = $('input.attribute-input[data-index="' + row.data('index') + '"]');

            container.css({
                opacity: 0.5
            });

            setTimeout(function () {
                var count = table.find('tbody > tr').length;

                input.remove();
                row.remove();

                if (count <= 1) {
                    table.find('tbody').append(
                        $('#no-attribute-row-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderAttributes();
            }, 250);
        }, orderAttributes = function () {
            $(".attributes-table").tableDnD({
                onDrop: function (table, row) {
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="attributes[' + $(selector).attr('data-index') + '][' + $(selector).attr('data-attribute-id') + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, changeAttributeValue = function (_this) {
            var value = _this.val();
            var index = _this.closest('tr').attr('data-index');
            var value_id = _this.closest('tr').attr('data-value-id');
            var pivot_id = _this.closest('tr').attr('id');

            clearTimeout(timer);

            timer = setTimeout(function() {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.products.save_custom_attribute_value') }}',
                    data: {
                        _token: token,
                        value_id: value_id,
                        pivot_id: pivot_id,
                        value: value
                    },
                    success : function(data) {
                        if (data.status == true) {
                            $('.attributes-request').find('input.attribute-custom-value[data-index="' + index + '"]').val(value);
                        } else {
                            init.FlashMessage('error', 'Could not assign the attribute! Please try again.');
                        }
                    }
                });
            }, 600);
        }, getLastAttributeIndex = function () {
            var inputs = $('.attributes-request').find('input.attribute-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initAttributeSelect = function () {
            $('.assign-attribute-set, .assign-attribute-id, .assign-value-id').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            listAttributes();

            $(document).on('click', 'a.attribute-assign', function (e) {
                e.preventDefault();

                assignAttribute($(this));
            });

            $(document).on('click', 'a.attribute-remove:not(.disabled)', function (e) {
                e.preventDefault();

                removeAttribute($(this));
            });

            $(document).on('change', 'select.assign-attribute-set', function (e) {
                loadAttributes($(this));
            });

            $(document).on('change', 'select.assign-attribute-id', function (e) {
                loadValues($(this));
            });

            $(document).on('click', 'textarea.attribute-value-change', function (e) {
                $(this).addClass('expanded');
            });

            $(document).on('mouseleave', 'textarea.attribute-value-change', function (e) {
                $(this).removeClass('expanded');
            });

            $(document).on('keyup', 'textarea.attribute-value-change', function (e) {
                changeAttributeValue($(this));
            });
        });
    </script>
@append