<div class="list-container">
    <div class="jstree"
         data-add-url="{{ route('admin.categories.create') }}"
         data-load-url="{{ route('admin.categories.tree.load') }}"
         data-list-url="{{ route('admin.categories.tree.list') }}"
         data-move-url="{{ route('admin.categories.tree.sort') }}"
         data-url-url="{{ route('admin.categories.tree.url') }}"
         data-token="{{ csrf_token() }}"
         data-has-url="true"
         data-container="#categories-container"
         data-table=".categories-table"
    ></div>
    {{--
    <span class="box full">
        Something wrong in tree? <a href="{{ route('admin.pages.tree.fix') }}">Fix it now!</a>
    </span>
    --}}
</div>