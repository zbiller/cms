<fieldset>
    <label>{{ $label }}</label>
    <div class="field-wrapper">
        @include('helpers::library.manager.new')
        @include('helpers::library.manager.current')
    </div>
    {!! form()->hidden($field, $current ? $current->path('original') : null, ['id' => 'library-input-' . $index]) !!}
</fieldset>

@include('helpers::library.manager.scripts')