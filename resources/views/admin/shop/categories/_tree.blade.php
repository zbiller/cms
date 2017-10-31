<div class="list-container">
    <div class="jstree"
         data-add-url="{{ route('admin.product_categories.create') }}"
         data-load-url="{{ route('admin.product_categories.tree.load') }}"
         data-list-url="{{ route('admin.product_categories.tree.list') }}"
         data-move-url="{{ route('admin.product_categories.tree.sort') }}"
         data-url-url="{{ route('admin.product_categories.tree.url') }}"
         data-token="{{ csrf_token() }}"
         data-has-url="true"
         data-container="#categories-container"
         data-table=".categories-table"
    ></div>
    <div class="box spaced">
        <span>Something wrong in tree?</span>&nbsp;
        <a href="{{ route('admin.product_categories.tree.fix') }}">Fix it now!</a>
    </div>
</div>