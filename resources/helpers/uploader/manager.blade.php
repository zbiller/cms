<fieldset>
    <label>{{ $label }}</label>
    <div class="field-wrapper">
        @include('helpers::uploader.manager.new')
        @include('helpers::uploader.manager.current')
    </div>
    {!! form()->hidden($field, $current ? $current->path('original') : null, ['id' => 'upload-input-' . $index]) !!}
</fieldset>

@include('helpers::uploader.manager.scripts')