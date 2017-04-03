@section('bottom_scripts')
    <script type="text/javascript">
        function initTooltip() {
            $('.tooltip').tooltipster({
                theme: 'tooltipster-punk'
            });
        }

        $(function () {
            var popup, container, url, model, field, path, type, accept, keyword, timer, cropper, style;

            var page = 2,
                index = '{{ $index }}',
                token = '{{ csrf_token() }}';

            var libraryLoad = function () {
                popup = $('#library-new-' + index);
                container = popup.find('.modal-tab.active div.uploads');
                url = '{{ route('admin.library.get') }}';
                type = popup.find('ul.modal-tabs li.active').data('type');
                accept = popup.find('ul.modal-tabs li.active').data('accept');
                keyword = popup.find('.modal-tab.active input.search').val();

                $.ajax({
                    type: 'GET',
                    url: url + '/' + type,
                    dataType: 'json',
                    data: {
                        _token : token,
                        accept: accept,
                        keyword: keyword
                    },
                    beforeSend:function(){
                        container.hide();
                    },
                    complete:function(){
                        container.slideDown(300);
                    },
                    success: function(data) {
                        page = 2;
                        container.html(data.html);
                        initTooltip();
                    }
                });
            }, libraryScroll = function () {
                popup = $('#library-new-' + index);
                container = popup.find('.modal-tab.active div.uploads');
                url = '{{ route('admin.library.get') }}';
                type = popup.find('ul.modal-tabs li.active').data('type');
                accept = popup.find('ul.modal-tabs li.active').data('accept');
                keyword = popup.find('.modal-tab.active input.search').val();

                if(container.scrollTop() + container.innerHeight() >= container[0].scrollHeight) {
                    $.ajax({
                        type : 'GET',
                        url: url + '/' + type + '?page=' + page,
                        data: {
                            _token: token,
                            accept: accept,
                            keyword: keyword
                        },
                        success : function(data) {
                            page += 1;
                            container.append(data.html);
                            initTooltip();
                        }
                    });
                }
            }, librarySearch = function () {
                popup = $('#library-new-' + index);
                container = popup.find('.modal-tab.active div.uploads');
                url = '{{ route('admin.library.get') }}';
                type = popup.find('ul.modal-tabs li.active').data('type');
                accept = popup.find('ul.modal-tabs li.active').data('accept');
                keyword = popup.find('.modal-tab.active input.search').val();

                clearInterval(timer);

                timer = setTimeout(function(){
                    $.ajax({
                        type: 'GET',
                        url: url + '/' + type,
                        dataType: 'json',
                        data: {
                            _token : token,
                            accept: accept,
                            keyword : keyword
                        },
                        success: function(data) {
                            page = 2;
                            container.html(data.html);
                            initTooltip();
                        }
                    });
                }, 300);
            }, libraryUpload = function () {
                popup = $('#library-new-' + index);
                accept = popup.find('ul.modal-tabs li.active').data('accept');

                $('#library-new-' + index + ' label.upload-btn > input[type="file"]').fileupload({
                    url: '{{ route('admin.library.upload') }}',
                    dataType: 'json',
                    formData: {
                        _token : token,
                        model: popup.data('model'),
                        field: popup.data('field'),
                        accept: accept
                    },
                    done: function (e, data) {
                        var message = popup.find('span.upload-message'),
                            progress = popup.find('.progress');

                        popup.find('.loading').fadeOut(300);

                        if (data.result.status === true) {
                            popup.find('#' + data.result.type + ' .uploads').prepend(data.result.html);
                            popup.find('#' + data.result.type + ' .uploads > p').remove();

                            popup.find('.modal-tab div.uploads > a').removeClass('active');
                            popup.find('#' + data.result.type + ' .uploads > a:first-of-type').addClass('active');

                            message.text(data.result.message).removeClass('error').addClass('success');
                            progress.find('.bar').removeClass('error').addClass('success');

                            initTooltip();
                        } else {
                            message.text(data.result.message).removeClass('success').addClass('error');
                            progress.find('.bar').removeClass('success').addClass('error');
                        }

                        setTimeout(function(){
                            progress.find('.bar').css('width', '0px').removeClass('success').removeClass('error');
                            message.text('');
                            progress.slideUp(500);
                        }, 5000);
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);

                        popup.find('.loading').fadeIn(300);
                        popup.find('.loading').show();
                        popup.find('.progress .bar').css('width', progress + '%');
                    }
                });
            }, librarySave = function () {
                popup = $('#library-new-' + index);
                container = popup.find('.modal-tab.active div.uploads');
                url = "{{ URL::route('admin.library.set') }}";
                model = popup.data('model');
                field = popup.data('field');
                path = container.find('a.active').data('path');

                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: {
                        _token : token,
                        path: path,
                        model: model,
                        field: field
                    },
                    beforeSend: function () {
                        popup.find('.loading').fadeIn(300);
                    },
                    complete: function () {
                        popup.find('.loading').fadeOut(300);
                    },
                    success: function(data) {
                        var input = $('#library-input-' + index),
                            button = $('a[data-popup-id="library-new-' + index + '"]'),
                            message = popup.find('span.upload-message');

                        if (data.status === true) {
                            input.val(data.path);
                            button.html(data.name);
                            $('.popup:visible').hide();
                        } else {
                            message.text(data.message).css('display', 'block').removeClass('success').addClass('error');

                            setTimeout(function(){
                                message.text('');
                            }, 5000);
                        }
                    }
                });
            }, libraryCrop = function () {
                popup = $('#library-current-' + index);
                cropper = $('.open-library-cropper-' + index);
                model = popup.data('model');
                field = popup.data('field');
                url = cropper.data('url');
                path = cropper.data('path');
                style = cropper.data('style');

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.library.crop') }}',
                    dataType: 'json',
                    data: {
                        _token : token,
                        index: index,
                        model: model,
                        field: field,
                        url: url,
                        path: path,
                        style: style
                    },
                    success: function(data) {
                        if (data.status === true) {
                            $('#library-crop-container-' + index).html(data.html);
                            $('#library-crop-' + index).show();
                        }
                    }
                });
            };

            //initial load
            $(document).on('click', '#open-library-new-' + index, function (e) {
                e.preventDefault();

                libraryLoad();
            });

            //click load
            $(document).on('click', '#library-new-' + index + ' ul.modal-tabs > li', function(e) {
                e.preventDefault();

                libraryLoad();
            });

            //scroll load
            $('#library-new-' + index + ' .modal-tab .uploads').on('scroll', function(e) {
                e.preventDefault();

                libraryScroll();
            });

            //search load
            $(document).on('keyup', '#library-new-' + index + ' .modal-tab.active input.search', function(e) {
                e.preventDefault();

                librarySearch();
            });

            //upload new
            $(document).on('click', '#library-new-' + index + ' label.upload-btn > input[type="file"]', function (e) {
                libraryUpload();
            });

            //save new
            $(document).on('click', '#library-save-' + index, function(e) {
                e.preventDefault();

                librarySave();
            });

            //cropper load
            $(document).on('click', '.open-library-cropper-' + index, function (e) {
                e.preventDefault();

                libraryCrop();
            });

            //delete current
            $(document).on('click', '#library-current-' + index + ' .library-delete', function(){
                $('#library-input-' + index).val('');
                $('#open-library-current-' + index).remove();
                $('#open-library-new-' + index).removeClass('half').addClass('full');
                $('.popup:visible').hide();
            });
        });
    </script>
@append