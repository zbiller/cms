<div class="list-container">
    <div id="tree"></div>

    <span class="box full">Something wrong in tree? <a href="{{ route('admin.pages.tree.fix') }}">Fix it now!</a></span>

    {!! form()->hidden('refresh_tree', 'no') !!}
</div>

@section('bottom_scripts')
    <script type="text/javascript">
        $(function(){
            var input = $('input[name="refresh_tree"]');
            input.val() == 'yes' ? location.reload(true) : input.val('yes');

            //init tree
            setTimeout(function () {
                $("#tree").jstree({
                    "core" : {
                        "themes" : {
                            "responsive" : false
                        },
                        "check_callback" : function(operation, node, node_parent, node_position, more) {
                            if (operation === "move_node") {
                                return (node.id != "id" && node_parent.id != "id");
                            }

                            return true;
                        },
                        'data': {
                            'url' : function(node) {
                                return "{{ route('admin.pages.tree.load') }}" + (parseInt(node.id) ? "/" + node.id : '');
                            }
                        }
                    },
                    "state" : { "key" : "state_key" },
                    "plugins" : ["dnd", "state", "types"]
                });
            }, 500);

            //load pages
            $("#tree").on('select_node.jstree', function (e, data) {
                var node = data.instance.get_node(data.selected);
                var search = $('section.filters input[name="search"]').val();
                var layout = $('section.filters select[name="layout"]').val();
                var type = $('section.filters select[name="type"]').val();
                var active = $('section.filters select[name="active"]').val();
                var start_date = $('section.filters input[name="start_date"]').val();
                var end_date = $('section.filters input[name="end_date"]').val();

                $('table.pages-table').css({
                    opacity: 0.5
                });

                $('a.btn.add').attr('href', '{{ route('admin.pages.create') }}');

                $.ajax({
                    url: "{{ URL::route('admin.pages.tree.list') }}" + "/" + (parseInt(node.id) ? node.id : ''),
                    type: "GET",
                    data: {
                        search: search,
                        layout: layout,
                        type: type,
                        active: active,
                        start_date: start_date,
                        end_date: end_date
                    },
                    success: function(data){
                        $('a.btn.add').attr('href', $('a.btn.add').attr('href') + '/' + (parseInt(node.id) ? node.id : ''));

                        $('section#pages-container').html(data);
                        $('table.pages-table').css({
                            opacity: 1
                        });
                    }
                });
            });

            //move pages
            $("#tree").on('move_node.jstree', function (e, data) {
                var _tree = $("#tree").jstree().get_json();
                var _node = data.node;
                var _data = {
                    page: parseInt(_node.id) ? _node.id : '',
                    children: _node.children,
                    parent: parseInt(data.parent) ? data.parent : '',
                    old_parent: parseInt(data.old_parent) ? data.old_parent : ''
                };

                $.ajax({
                    url: "{{ URL::route('admin.pages.tree.sort') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tree: _tree
                    },
                    dataType: 'json',
                    success: function(data){
                        if (data > 0) {
                            $.ajax({
                                url: "{{ URL::route('admin.pages.tree.url') }}",
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    data: _data
                                },
                                dataType: 'json',
                                success: function(data){}
                            });
                        }
                    }
                });
            });

        });
    </script>
@append