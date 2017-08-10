@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}
{!! form()->hidden('set_id', $set->id) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name', 'Name', null, $item->exists ? [] : ['id' => 'slug-from']) !!}
    {!! form_admin()->text('slug', 'Slug', null, $item->exists ? [] : ['id' => 'slug-to']) !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->select('filterable', 'Global', $filters) !!}

    <fieldset>
        <label>Only On</label>
        @php($selectedCategories = $item->categories->pluck('id')->toArray())
        <select name="categories[]" class="select-input" multiple="multiple">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" style="padding-left: {{ 6 + ($category->depth * 20) }}px" {{ in_array($category->id, $selectedCategories) ? 'selected="selected"' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </fieldset>
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Shop\AttributeRequest::class, '.form') !!}
@append