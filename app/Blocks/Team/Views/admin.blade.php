{!! form_admin()->text('metadata[title]', 'Title') !!}
{!! form_admin()->textarea('metadata[description]', 'Description') !!}

<a id="multiple-add-item" class="btn dark-blue full centered no-margin-left no-margin-right no-margin-bottom">
    <i class="fa fa-plus"></i>&nbsp; Add new item
</a>
<div id="multiple-items-container">
    @if($item->exists && isset($item->metadata->items))
        @foreach($item->metadata->items as $index => $_item)
            <div class="multiple-item" data-index="{{ $index }}">
                {!! block()->buttons() !!}

                {!! form_admin()->text('metadata[items][' . $index . '][name]', 'Name') !!}
                {!! uploader()->field('metadata[items][' . $index . '][team_image]')->label('Image')->model($item)->types('image')->manager() !!}
                {!! form_admin()->text('metadata[items][' . $index . '][caption]', 'Image Caption') !!}
                {!! form_admin()->text('metadata[items][' . $index . '][position]', 'Position') !!}
                {!! form_admin()->textarea('metadata[items][' . $index . '][biography]', 'Biography') !!}
                {!! form_admin()->text('metadata[items][' . $index . '][button_text]', 'Button Text') !!}
                {!! form_admin()->text('metadata[items][' . $index . '][button_url]', 'Button URL') !!}
            </div>
        @endforeach
    @endif
</div>
<script type="x-template" id="multiple-items-template">
    <div class="multiple-item" data-index="#index">
        {!! block()->buttons() !!}

        {!! form_admin()->text('metadata[items][#index][name]', 'Name', '#name#') !!}
        {!! uploader()->field('metadata[items][#index][team_image]')->label('Image')->model($item)->types('image')->manager() !!}
        {!! form_admin()->text('metadata[items][#index][caption]', 'Caption', '#caption#') !!}
        {!! form_admin()->text('metadata[items][#index][position]', 'Position', '#position#') !!}
        {!! form_admin()->textarea('metadata[items][#index][biography]', 'Biography', '#biography#') !!}
        {!! form_admin()->text('metadata[items][#index][button_text]', 'Button Text', '#button_text#') !!}
        {!! form_admin()->text('metadata[items][#index][button_url]', 'Button URL', '#button_url#') !!}
    </div>
</script>