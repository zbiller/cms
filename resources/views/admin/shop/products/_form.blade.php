@if($item->exists)
    @if(isset($on_draft) || isset($on_limbo_draft) || isset($on_revision))
        {!! form_admin()->model($item, ['method' => isset($on_draft) || isset($on_revision) ? 'POST' : 'PUT','class' => 'form', 'files' => true]) !!}
    @else
        {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
    @endif
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Shop\Product::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\Shop\ProductRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}
{!! form()->hidden('_back', route('admin.products.drafts')) !!}

<div id="tab-1" class="tab">
    <fieldset>
        <label>Main Category</label>
        <select name="category_id" class="select-input">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" style="padding-left: {{ 6 + ($category->depth * 20) }}px" {{ $item->exists && $category->id == $item->category_id ? 'selected="selected"' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </fieldset>
    <fieldset>
        <label>Other Categories</label>
        @php($selectedCategories = $item->categories()->pluck('category_id')->toArray())
        <select name="categories[]" class="select-input" multiple="multiple">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" style="padding-left: {{ 6 + ($category->depth * 20) }}px" {{ in_array($category->id, $selectedCategories) ? 'selected="selected"' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </fieldset>
    {!! form_admin()->text('sku') !!}
    {!! form_admin()->text('name', 'Name', null, $item->exists ? [] : ['id' => 'slug-from']) !!}
    {!! form_admin()->text('slug', 'Slug', null, $item->exists ? [] : ['id' => 'slug-to']) !!}
    {!! form_admin()->number('price') !!}
    {!! form_admin()->select('currency_id', 'Currency', $currencies->pluck('code', 'id')) !!}
    {!! form_admin()->number('quantity') !!}
    {!! form_admin()->select('active', 'Active', $actives) !!}
    {!! form_admin()->number('views', 'Views Count', null, ['disabled' => 'disabled']) !!}
    {!! form_admin()->number('sales', 'Sales Count', null, ['disabled' => 'disabled']) !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->editor('content') !!}
</div>
<div id="tab-3" class="tab">
    {!! form_admin()->text('metadata[meta][title]', 'Title') !!}
    {!! uploader()->field('metadata[meta][image]')->label('Image')->model($item)->types('image')->manager() !!}
    {!! form_admin()->textarea('metadata[meta][description]', 'Description') !!}
    {!! form_admin()->textarea('metadata[meta][keywords]', 'Keywords') !!}
</div>
<div id="tab-4" class="tab">
    @if(!isset($on_revision))
        <div class="dropzone" style="margin: 0 0 20px 0;">
            <div class="ddTitle" style="width: 380px;">
                Drag & drop images or click the area to upload
            </div>
        </div>
    @endif
    <a id="multiple-add-item" class="btn dark-blue full centered no-margin-left no-margin-right no-margin-bottom">
        <i class="fa fa-plus"></i>&nbsp; Add new image
    </a>
    <div id="multiple-items-container">
        @if($item->exists && $item->images->count())
            @foreach($item->images as $index => $image)
                <div class="multiple-item" data-index="{{ $index }}">
                    {!! block()->buttons() !!}
                    {!! uploader()->field('metadata[images][' . $index . '][image]')->label('<img src="' . uploaded($image)->thumbnail() . '" width="150" />')->model($item)->types('image')->manager() !!}
                </div>
            @endforeach
        @endif
    </div>
    <script type="x-template" id="multiple-items-template">
        <div class="multiple-item" data-index="#index">
            {!! block()->buttons() !!}
            {!! uploader()->field('metadata[images][#index][image]')->label('-')->model($item)->types('image')->manager() !!}
        </div>
    </script>
</div>
<div id="tab-5" class="tab tab-attributes">
    @include('admin.shop.products.attributes.assign', ['item' => $item, 'draft' => isset($draft) ? $draft : null, 'revision' => isset($revision) ? $revision : null, 'disabled' => isset($on_revision) ? true : false])
</div>
<div id="tab-6" class="tab tab-discounts">
    @include('admin.shop.products.discounts.assign', ['item' => $item, 'draft' => isset($draft) ? $draft : null, 'revision' => isset($revision) ? $revision : null, 'disabled' => isset($on_revision) ? true : false])
</div>
<div id="tab-7" class="tab tab-taxes">
    @include('admin.shop.products.taxes.assign', ['item' => $item, 'draft' => isset($draft) ? $draft : null, 'revision' => isset($revision) ? $revision : null, 'disabled' => isset($on_revision) ? true : false])
</div>


@if($item->exists)
    {!! block()->container($item, isset($on_draft) ? $draft : null, isset($on_revision) ? $revision : null, isset($on_revision) ? true : false) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->container($item) !!}
        {!! revision()->container($item) !!}
    @endif
@endif

{!! form_admin()->close() !!}

@section('bottom_scripts')
    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! JsValidator::formRequest(App\Http\Requests\Shop\ProductRequest::class, '.form') !!}
    @endif

    <script type="text/javascript">
        $(".dropzone").dropzone({
            url: '{{ route('admin.products.upload') }}',
            acceptedFiles: "image/jpeg,image/png,image/gif",
            success: function (file, response) {
                if (response.status == true) {
                    $('.form').append('<input type="hidden" name="metadata[images][][image]" value="' + response.file + '" />');

                    return file.previewElement.classList.add("dz-success");
                } else {
                    var node, _i, _len, _ref, _results;
                    var message = response.message;

                    file.previewElement.classList.add("dz-error");

                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];

                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        node = _ref[_i];
                        _results.push(node.textContent = message);
                    }

                    return _results;
                }
            },
            sending: function(file, xhr, formData) {
                formData.append('_token', '{{ csrf_token() }}');
            }
        });
    </script>
@append