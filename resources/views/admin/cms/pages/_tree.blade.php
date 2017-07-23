<div class="list-container">
    <div class="jstree"
         data-add-url="{{ route('admin.pages.create') }}"
         data-load-url="{{ route('admin.pages.tree.load') }}"
         data-list-url="{{ route('admin.pages.tree.list') }}"
         data-move-url="{{ route('admin.pages.tree.sort') }}"
         data-url-url="{{ route('admin.pages.tree.url') }}"
         data-token="{{ csrf_token() }}"
         data-has-url="true"
         data-container="#pages-container"
         data-table=".pages-table"
    ></div>
    {{--
    <span class="box full">
        Something wrong in tree? <a href="{{ route('admin.pages.tree.fix') }}">Fix it now!</a>
    </span>
    --}}
</div>