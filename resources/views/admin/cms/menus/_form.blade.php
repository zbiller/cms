{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! form_admin()->select('type', 'Type', [null => ''] + $types) !!}

    <div id="menu-type-url" class="menu-types">
        {!! form_admin()->text('url') !!}
    </div>
    <div id="menu-type-custom" class="menu-types">
        {!! form_admin()->select('menuable_id', 'Url') !!}
    </div>

    {!! form_admin()->select('active', 'Active', $actives) !!}
    {!! form_admin()->select('metadata[new_window]', 'Open In New Window', $windows, ($item->exists && isset($item->metadata->new_window) ? $item->metadata->new_window : null)) !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\MenuRequest::class, '.form') !!}

    <script type="text/javascript">
        $(function () {
            if ($('select[name="type"]').val() != '') {
                selectMenuType($('select[name="type"]').val());
            }

            $(document).on('change', 'select[name="type"]', function () {
                selectMenuType($(this).val());
            });
        });

        /**
         * @param type
         * @return void
         */
        function selectMenuType(type) {
            $('.menu-types').hide();

            if (type == '{{ \App\Models\Cms\Menu::TYPE_URL }}') {
                $('#menu-type-url').show();
            } else {
                var chosen = $('select[name="menuable_id"]');

                chosen.empty();

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.menus.entity') }}' + '/' + type,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $.each(data.attributes, function (index, attribute){
                                chosen.append(
                                    '<option value="' + attribute.value + '"' + (attribute.value == '{{ $item->menuable_id }}' ? ' selected' : '') + '>' + attribute.name + '</option>'
                                ).trigger('chosen:updated');
                            });
                        }

                        $('#menu-type-custom').show();
                    }
                });
            }
        }
    </script>
@append