@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';
        var revisionsTab = $('div#tab-revisions');
        var revisionsTable = $('table.revisions-table');

        var listRevisions = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.revisions.get') }}',
                data: {
                    _token: token,
                    revisionable_id: revisionsTable.data('revisionable-id'),
                    revisionable_type: revisionsTable.data('revisionable-type'),
                    route: '{{ $route }}',
                    parameters: '{!! json_encode($parameters) !!}'
                },
                beforeSend: function () {
                    revisionsTable.hide();
                    revisionsTab.find('.loading').fadeIn(300);
                },
                complete: function () {
                    revisionsTab.find('.loading').fadeOut(300);

                    setTimeout(function () {
                        revisionsTable.fadeIn(300);
                    }, 400);
                },
                success : function(data) {
                    if (data.status == true) {
                        revisionsTable.html(data.html);
                    } else {
                        revisionsTab.hide();
                        init.FlashMessage('error', 'Could not load the revisions! Please try again.');
                    }
                },
                error: function (err) {
                    revisionsTab.hide();
                    init.FlashMessage('error', 'Could not load the revisions! Please try again.');
                }
            });
        }, rollbackRevision = function (_this) {
            $.ajax({
                type : 'POST',
                url: _this.attr('href'),
                data: {
                    _token: token
                },
                beforeSend: function () {
                    revisionsTable.css({opacity: 0.5});
                },
                success : function(data) {
                    if (data.status == true) {
                        location.reload();
                    } else {
                        init.FlashMessage('error', 'Could not rollback the revision! Please try again.');
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not rollback the revision! Please try again.');
                }
            });
        }, deleteRevision = function (_this) {
            $.ajax({
                type : 'DELETE',
                url: _this.attr('href'),
                data: {
                    _token: token,
                    revisionable_id: revisionsTable.data('revisionable-id'),
                    revisionable_type: revisionsTable.data('revisionable-type'),
                    route: '{{ $route }}',
                    parameters: '{!! json_encode($parameters) !!}'
                },
                beforeSend: function () {
                    revisionsTable.css({opacity: 0.5});
                },
                complete: function () {
                    revisionsTable.css({opacity: 1});
                },
                success : function(data) {
                    if (data.status == true) {
                        revisionsTable.html(data.html);
                    } else {
                        init.FlashMessage('error', 'Could not remove the revision! Please try again.');
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not remove the revision! Please try again.');
                }
            });
        };

        $(function () {
            listRevisions();

            $(document).on('click', '.revision-rollback', function (e) {
                e.preventDefault();

                if (confirm('Are you sure you want to rollback this revision?')) {
                    rollbackRevision($(this));
                }
            });

            $(document).on('click', '.revision-delete', function (e) {
                e.preventDefault();

                deleteRevision($(this));
            });
        });
    </script>
@append