<fieldset>
    <label>{{ $label }}</label>
    <div class="field-wrapper">
        @include('helpers::uploader.partials.new')
        @include('helpers::uploader.partials.current')
    </div>
    {!! form()->hidden($field, $current ? $current->path('original') : null, ['id' => 'upload-input-' . $index, 'class' => 'upload-input']) !!}
</fieldset>

@include('helpers::uploader.partials.scripts')