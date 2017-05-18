@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';
        var draftsTab = $('div#tab-drafts');
        var draftsTable = $('table.drafts-table');

        var listDrafts = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.drafts.get') }}',
                data: {
                    _token: token,
                    draftable_id: draftsTable.data('draftable-id'),
                    draftable_type: draftsTable.data('draftable-type'),
                    route: '{{ $route }}'
                },
                beforeSend: function () {
                    draftsTable.hide();
                    draftsTab.find('.loading').fadeIn(300);
                },
                complete: function () {
                    draftsTab.find('.loading').fadeOut(300);

                    setTimeout(function () {
                        draftsTable.fadeIn(300);
                    }, 400);
                },
                success : function(data) {
                    if (data.status == true) {
                        draftsTable.html(data.html);
                    } else {
                        draftsTab.hide();
                        init.FlashMessage('error', 'Could not load the drafts! Please try again.');
                    }
                },
                error: function (err) {
                    draftsTab.hide();
                    init.FlashMessage('error', 'Could not load the drafts! Please try again.');
                }
            });
        }, publishDraft = function (_this) {
            $.ajax({
                type : 'POST',
                url: _this.attr('href'),
                data: {
                    _token: token
                },
                beforeSend: function () {
                    draftsTable.css({opacity: 0.5});
                },
                success : function(data) {
                    if (data.status == true) {
                        location.reload();
                    } else {
                        init.FlashMessage('error', 'Could not publish the record to the given draft! Please try again.');
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not publish the record to the given draft! Please try again.');
                }
            });
        }, deleteDraft = function (_this) {
            $.ajax({
                type : 'DELETE',
                url: _this.attr('href'),
                data: {
                    _token: token,
                    draftable_id: draftsTable.data('draftable-id'),
                    draftable_type: draftsTable.data('draftable-type'),
                    route: '{{ $route }}'
                },
                beforeSend: function () {
                    draftsTable.css({opacity: 0.5});
                },
                complete: function () {
                    draftsTable.css({opacity: 1});
                },
                success : function(data) {
                    if (data.status == true) {
                        draftsTable.html(data.html);
                    } else {
                        init.FlashMessage('error', 'Could not remove the draft! Please try again.');
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not remove the draft! Please try again.');
                }
            });
        };

        $(function () {
            listDrafts();

            $(document).on('click', 'a[href="#tab-drafts"]', function (e) {

                $(this).off(e);
            });

            $(document).on('click', '.draft-publish', function (e) {
                e.preventDefault();

                if (confirm('Are you sure you want to publish this draft?')) {
                    publishDraft($(this));
                }

                return false;
            });

            $(document).on('click', '.draft-delete', function (e) {
                e.preventDefault();

                deleteDraft($(this));
            });
        });
    </script>
@append