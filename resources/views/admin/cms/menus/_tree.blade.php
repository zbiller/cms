<div class="list-container">
    <div class="jstree"
         data-add-url="{{ route('admin.menus.create', ['location' => $location]) }}"
         data-load-url="{{ route('admin.menus.tree.load', ['location' => $location]) }}"
         data-list-url="{{ route('admin.menus.tree.list', ['location' => $location]) }}"
         data-move-url="{{ route('admin.menus.tree.sort') }}"
         data-token="{{ csrf_token() }}"
         data-has-url="false"
         data-container="#menus-container"
         data-table=".menus-table"
    ></div>
    <div class="box spaced">
        <span>Something wrong in tree?</span>&nbsp;
        <a href="{{ route('admin.menus.tree.fix') }}">Fix it now!</a>
    </div>
</div>