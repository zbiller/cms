<a id="block-add-item" class="btn dark-blue full centered no-margin-left no-margin-right no-margin-bottom">
    <i class="fa fa-plus"></i>&nbsp; Add new item
</a>
<div id="block-items-container">
    @if($item->exists && isset($item->metadata->items))
        @foreach($item->metadata->items as $index => $_item)
            <div class="block-item" data-index="{{ $index }}">
                {!! block()->buttons() !!}

                {!! form_admin()->text('metadata[items][' . $index . '][title]', 'Title') !!}
                {!! form_admin()->textarea('metadata[items][' . $index . '][content]', 'Content') !!}
                {!! form_admin()->text('metadata[items][' . $index . '][button_text]', 'Button Text') !!}
                {!! form_admin()->text('metadata[items][' . $index . '][button_url]', 'Button Url') !!}
            </div>
        @endforeach
    @endif
</div>
<script type="x-template" id="block-items-template">
    <div class="block-item" data-index="#index">
        {!! block()->buttons() !!}

        {!! form_admin()->text('metadata[items][#index][title]', 'Title', '#title#') !!}
        {!! form_admin()->textarea('metadata[items][#index][content]', 'Content', '#content#') !!}
        {!! form_admin()->text('metadata[items][#index][button_text]', 'Button Text', '#button_text#') !!}
        {!! form_admin()->text('metadata[items][#index][button_url]', 'Button Url', '#button_url#') !!}
    </div>
</script>