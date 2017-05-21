{!! form_admin()->text('metadata[title]', 'Title') !!}
{!! form_admin()->textarea('metadata[description]', 'Description') !!}

<a id="block-add-item" class="btn dark-blue full centered no-margin-left no-margin-right no-margin-bottom">
    <i class="fa fa-plus"></i>&nbsp; Add new item
</a>
<div id="block-items-container">
    @if($item->exists && isset($item->metadata->items))
        @foreach($item->metadata->items as $index => $_item)
            <div class="block-item" data-index="{{ $index }}">
                {!! block()->buttons() !!}

                {!! uploader()->field('metadata[items][' . $index . '][client_image]')->label('Image')->model($item)->types('image')->manager() !!}
                {!! form_admin()->text('metadata[items][' . $index . '][caption]', 'Caption') !!}
            </div>
        @endforeach
    @endif
</div>
<script type="x-template" id="block-items-template">
    <div class="block-item" data-index="#index">
        {!! block()->buttons() !!}

        {!! uploader()->field('metadata[items][#index][client_image]')->label('Image')->model($item)->types('image')->manager() !!}
        {!! form_admin()->text('metadata[items][#index][caption]', 'Caption', '#caption#') !!}
    </div>
</script>