{!! form()->hidden('touch_attributes', true) !!}

<div class="attributes-container">
    <table class="assign-table attributes-table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr class="even nodrag nodrop">
                <td>Name</td>
                <td>Value</td>
                <td class="actions-assign">Actions</td>
            </tr>
        </thead>
        <tbody>
            @if($attributes->count())
                @foreach($attributes as $attribute)
                    @php($pivot = $attribute->pivot)
                    @php($value = \App\Models\Shop\Attribute\Value::find($pivot->value_id))
                    <tr id="{{ $pivot->id }}" data-attribute-id="{{ $attribute->id }}" data-value-id="{{ $pivot->value_id }}" data-index="{{ $pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                        <td>{{ $attribute->name ?: 'N/A' }}</td>
                        <td>{!! form()->textarea('', $pivot->value ?: ($value && $value->exists ? $value->value : ''), ['class' => 'assign-attribute-value-update assign-textarea-update']) !!}</td>
                        <td>
                            <a href="{{ route('admin.attributes.edit', ['set' => $attribute->set, 'attribute' => $attribute]) }}" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                                <i class="fa fa-eye"></i>&nbsp; View
                            </a>
                            <a href="#" class="attribute-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                                <i class="fa fa-times"></i>&nbsp; Remove
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="no-attributes-assigned no-assignments nodrag nodrop">
                    <td colspan="10">
                        There are no attributes assigned to this product
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($disabled === false)
        <div class="assign-container">
            <div class="assign-content full">
                <select class="assign-set assign-select full">
                    <option selected="selected" disabled>Set</option>
                    @foreach($sets as $set)
                        <option value="{{ $set->id }}">{{ $set->name }}</option>
                    @endforeach
                </select>
                <select class="assign-attribute assign-select full">
                    <option selected="selected" disabled>Attribute</option>
                </select>
                <select class="assign-value assign-select full">
                    <option selected="selected" disabled>Value</option>
                </select>
                <textarea class="assign-textarea assign-attribute-value" placeholder="Custom Value"></textarea>
            </div>
            <div class="assign-footer full">
                <a href="#" class="attribute-assign btn green no-margin right">
                    <i class="fa fa-plus"></i>&nbsp; Assign
                </a>
            </div>
        </div>
    @endif

    <div class="attributes-request">
        @if($attributes->count() > 0)
            @foreach($attributes as $index => $attribute)
                @php($pivot = $attribute->pivot)
                {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . ']', $pivot->product_id, ['class' => 'attribute-input', 'data-index' => $pivot->id]) !!}
                {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . '][value_id]', $pivot->value_id, ['class' => 'attribute-input', 'data-index' => $pivot->id]) !!}
                {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . '][value]', $pivot->value, ['class' => 'attribute-input attribute-custom-value', 'data-index' => $pivot->id]) !!}
                {!! form()->hidden('attributes[' . $pivot->id . '][' . $attribute->id . '][ord]', $pivot->ord, ['class' => 'attribute-input', 'data-index' => $pivot->id]) !!}
            @endforeach
        @endif
    </div>
</div>

<script type="x-template" id="one-attribute-template">
    <tr id="#index#" data-attribute-id="#attribute_id#" data-value-id="#value_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
        <td>
            #attribute_name#
        </td>
        <td>
            {!! form()->textarea('', '#attribute_value#', ['class' => 'assign-attribute-value-update assign-textarea-update']) !!}
        </td>
        <td>
            <a href="#attribute_url#" class="assign-view btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                <i class="fa fa-eye"></i>&nbsp; View
            </a>
            <a href="#" class="attribute-remove assign-remove btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                <i class="fa fa-times"></i>&nbsp; Remove
            </a>
        </td>
    </tr>
</script>
<script type="x-template" id="no-attributes-template">
    <tr class="no-attributes-assigned nodrag nodrop">
        <td colspan="10">
            There are no attributes assigned to this product
        </td>
    </tr>
</script>
<script type="x-template" id="attribute-request-template">
    {!! form()->hidden('attributes[#index#][#attribute_id#]', '#attribute_id#', ['class' => 'attribute-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('attributes[#index#][#attribute_id#][value_id]', '#value_id#', ['class' => 'attribute-input', 'data-index' => '#index#']) !!}
    {!! form()->hidden('attributes[#index#][#attribute_id#][value]', '#attribute_value#', ['class' => 'attribute-input attribute-custom-value', 'data-index' => '#index#']) !!}
    {!! form()->hidden('attributes[#index#][#attribute_id#][ord]', '#attribute_ord#', ['class' => 'attribute-input', 'data-index' => '#index#']) !!}
</script>

@php($item->clearQueryCache())

@section('bottom_scripts')
    <script type="text/javascript">
        var timer;
        var token = '{{ csrf_token() }}';

        var loadAttributes = function (_this) {
            var set_id = _this.val();
            var attribute_select = $('select.assign-attribute');
            var value_select = $('select.assign-value');

            if (set_id) {
                $.ajax({
                    type : 'GET',
                    url: '{{ route('admin.attributes.get') }}' + '/' + set_id,
                    success : function(data) {
                        attribute_select.empty();

                        $.each(data.items, function (index, attribute) {
                            attribute_select.append('<option value="' + attribute.id + '">' + attribute.name + '</option>');

                            if (index == 0) {
                                value_select.empty();

                                $.each(attribute.values, function (i, value) {
                                    value_select.append('<option value="' + value.id + '">' + value.value + '</option>');
                                });
                            }
                        });

                        attribute_select.trigger("chosen:updated");
                        value_select.trigger("chosen:updated");
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not load the attributes! Please try again.');
                    }
                });
            }
        }, loadAttributeValues = function (_this) {
            var set_id = $('select.assign-set').val();
            var attribute_id = _this.val();
            var value_select = $('select.assign-value');

            if (set_id && attribute_id) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.attribute_values.get') }}' + '/' + set_id + '/' + attribute_id,
                    success: function (data) {
                        value_select.empty();

                        $.each(data.items, function (index, value) {
                            value_select.append('<option value="' + value.id + '">' + value.value + '</option>');
                        });

                        value_select.trigger("chosen:updated");
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not load the attribute values! Please try again.');
                    }
                });
            }
        }, assignAttribute = function (_this) {
            var container = _this.closest('div.attributes-container');
            var table = container.find('table.attributes-table');
            var set_id = container.find('select.assign-set').val();
            var attribute_id = container.find('select.assign-attribute').val();
            var value_id = container.find('select.assign-value').val();
            var value = container.find('textarea.assign-attribute-value').val();

            if (set_id && attribute_id && value_id) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.products.load_attribute') }}',
                    data: {
                        _token: token,
                        set_id: set_id,
                        attribute_id: attribute_id,
                        value_id: value_id,
                        value: value
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
                            table.find('tr.no-attributes-assigned').remove();

                            table.find('tbody').append(
                                $('#one-attribute-template').html()
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
                            container.find('textarea.assign-attribute-value').val('');
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
            var container = _this.closest('div.attributes-container');
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
                        $('#no-attributes-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderAttributes();
            }, 250);
        }, orderAttributes = function () {
            $("table.attributes-table").tableDnD({
                onDrop: function (table, row) {
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="attributes[' + $(selector).attr('data-index') + '][' + $(selector).attr('data-attribute-id') + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, updateAttributeValue = function (_this) {
            var index = _this.closest('tr').attr('data-index');
            var value_id = _this.closest('tr').attr('data-value-id');
            var pivot_id = _this.closest('tr').attr('id');
            var value = _this.val();

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
                            $('div.attributes-request').find('input.attribute-custom-value[data-index="' + index + '"]').val(value);
                        } else {
                            init.FlashMessage('error', 'Could not update the attribute\'s value! Please try again.');
                        }
                    }
                });
            }, 600);
        }, getLastAttributeIndex = function () {
            var inputs = $('div.attributes-request').find('input.attribute-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initAttributeSelect = function () {
            $('select.assign-set, select.assign-attribute, select.assign-value').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            orderAttributes();
            initAttributeSelect();

            $(document).on('click', 'a.attribute-assign', function (e) {
                e.preventDefault();

                assignAttribute($(this));
            });

            $(document).on('click', 'a.attribute-remove:not(.disabled)', function (e) {
                e.preventDefault();

                removeAttribute($(this));
            });

            $(document).on('change', 'select.assign-set', function (e) {
                loadAttributes($(this));
            });

            $(document).on('change', 'select.assign-attribute', function (e) {
                loadAttributeValues($(this));
            });

            $(document).on('click', 'textarea.assign-attribute-value-update', function (e) {
                $(this).addClass('expanded');
            });

            $(document).on('mouseleave', 'textarea.assign-attribute-value-update', function (e) {
                $(this).removeClass('expanded');
            });

            $(document).on('keyup', 'textarea.assign-attribute-value-update', function (e) {
                updateAttributeValue($(this));
            });
        });
    </script>
@append