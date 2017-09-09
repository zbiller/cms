@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}
{!! form()->hidden('user_id', $user->id) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('country_id', 'Country', $countries->pluck('name', 'id')->toArray(), null) !!}
    {!! form_admin()->select('state_id', 'State', ['' => 'None'] + ($item->exists && isset($states) ? $states->pluck('name', 'id')->toArray() : []), $item->exists ? $item->state_id : null) !!}
    {!! form_admin()->select('city_id', 'City', ['' => 'None'] + ($item->exists && isset($cities) ? $cities->pluck('name', 'id')->toArray() : []), $item->exists ? $item->city_id : null) !!}
    {!! form_admin()->textarea('address', 'Address') !!}
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Auth\AddressRequest::class, '.form') !!}

    <script type="text/javascript">
        var countrySelect = $('select[name="country_id"]');
        var stateSelect = $('select[name="state_id"]');
        var citySelect = $('select[name="city_id"]');

        countrySelect.change(function () {
            getStates($(this).val());
        });

        stateSelect.change(function () {
            getCities(countrySelect.val(), $(this).val());
        });

        var getStates = function (countryId) {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.states.get') }}' + '/' + countryId,
                success : function(data) {
                    if (data.status == true) {
                        stateSelect.empty();
                        citySelect.empty();

                        stateSelect.append('<option value="">None</option>');
                        citySelect.append('<option value="">None</option>');

                        $.each(data.states, function (index, state) {
                            stateSelect.append('<option value="' + state.id + '">' + state.name + '</option>');
                        });

                        $.each(data.cities, function (index, city) {
                            citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });

                        stateSelect.trigger("chosen:updated");
                        citySelect.trigger("chosen:updated");
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not load the states! Please try again.');
                }
            });
        }, getCities = function (countryId, stateId) {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.cities.get') }}' + '/' + countryId + '/' + stateId,
                success : function(data) {
                    if (data.status == true) {
                        citySelect.empty();

                        citySelect.append('<option value="">None</option>');

                        $.each(data.cities, function (index, city) {
                            citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });

                        citySelect.trigger("chosen:updated");
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not load the cities! Please try again.');
                }
            });
        };
    </script>
@append