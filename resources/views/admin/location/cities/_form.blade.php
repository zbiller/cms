{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('country_id', 'Country', $countries->pluck('name', 'id')) !!}
    {!! form_admin()->select('state_id', 'State', $item->exists && isset($states) ? $states->pluck('name', 'id') : []) !!}
    {!! form_admin()->text('name') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\CityRequest::class, '.form') !!}

    <script type="text/javascript">
        var country = $('select[name="country_id"]');
        var getStates = function (_this) {
            var url = '{{ route('admin.cities.get_states') }}' + '/' + country.val();
            var select = $('select[name="state_id"]');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(data) {
                    if (data.status === true) {
                        select.empty();

                        $.each(data.states, function (id, name) {
                            select.append('<option value="' + id + '">' + name + '</option>');
                        });

                        select.trigger("chosen:updated");
                    }
                }
            });
        };

        if (country.length) {
            @if(!$item->exists)
                if (country.val()) {
                    getStates();
                }
            @endif

            country.change(function () {
                getStates();
            });
        }
    </script>
@append