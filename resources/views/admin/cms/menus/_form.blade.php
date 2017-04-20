{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! form_admin()->select('type', 'Type', [null => ''] + $types, null, ['id' => 'menu-type-select', 'data-url' => route('admin.menus.entity'), 'data-selected' => $item->exists ? $item->menuable_id : null, 'data-default-type' => \App\Models\Cms\Menu::TYPE_URL]) !!}

    <div id="menu-type-default" class="menu-types">
        {!! form_admin()->text('url') !!}
    </div>
    <div id="menu-type-custom" class="menu-types">
        {!! form_admin()->select('menuable_id', 'Url') !!}
    </div>

    {!! form_admin()->select('active', 'Active', $actives) !!}
    {!! form_admin()->select('metadata[new_window]', 'Open In New Window', $windows, ($item->exists && isset($item->metadata->new_window) ? $item->metadata->new_window : null)) !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\MenuRequest::class, '.form') !!}
@append