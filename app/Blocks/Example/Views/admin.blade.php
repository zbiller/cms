{!! form_admin()->text('metadata[title]', 'Title') !!}
{!! uploader()->field('metadata[image]')->label('Image')->model($item)->types('image')->manager() !!}
{!! form_admin()->select('metadata[active]', 'Active', ['0' => 'No', '1' => 'Yes']) !!}
{!! form_admin()->calendar('metadata[date]', 'Date') !!}
{!! form_admin()->editor('metadata[content]', 'Content') !!}

<a id="multiple-add-item" class="btn dark-blue full centered no-margin-left no-margin-right no-margin-bottom">
    <i class="fa fa-plus"></i>&nbsp; Add new item
</a>
<div id="multiple-items-container">
    @if($item->exists && isset($item->metadata->items))
        @foreach($item->metadata->items as $index => $_item)
            <div class="multiple-item" data-index="{{ $index }}">
                {!! block()->buttons() !!}

                {!! form_admin()->text('metadata[items][' . $index . '][title]', 'Title') !!}
                {!! form_admin()->text('metadata[items][' . $index . '][subtitle]', 'Subtitle') !!}
                {!! form_admin()->select('metadata[items][' . $index . '][active]', 'Active', ['0' => 'No', '1' => 'Yes']) !!}
                {!! form_admin()->calendar('metadata[items][' . $index . '][date]', 'Date') !!}
                {!! form_admin()->time('metadata[items][' . $index . '][time]', 'Time') !!}
                {!! form_admin()->color('metadata[items][' . $index . '][color]', 'Color') !!}
                {!! form_admin()->editor('metadata[items][' . $index . '][content]', 'Content') !!}
                {!! uploader()->field('metadata[items][' . $index . '][image]')->label('Image')->model($item)->types('image')->manager() !!}
            </div>
        @endforeach
    @endif
</div>
<script type="x-template" id="multiple-items-template">
    <div class="multiple-item" data-index="#index">
        {!! block()->buttons() !!}

        {!! form_admin()->text('metadata[items][#index][title]', 'Title', '#title#') !!}
        {!! form_admin()->text('metadata[items][#index][subtitle]', 'Subtitle', '#subtitle#') !!}
        {!! form_admin()->select('metadata[items][#index][active]', 'Active', ['0' => 'No', '1' => 'Yes'], '#active#') !!}
        {!! form_admin()->calendar('metadata[items][#index][date]', 'Date', '#date#') !!}
        {!! form_admin()->time('metadata[items][#index][time]', 'Time', '#time#') !!}
        {!! form_admin()->color('metadata[items][#index][color]', 'Color', '#color#') !!}
        {!! form_admin()->editor('metadata[items][#index][content]', 'Content', '#content#') !!}
        {!! uploader()->field('metadata[items][#index][image]')->label('Image')->model($item)->types('image')->manager() !!}
    </div>
</script>