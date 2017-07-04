@section('bottom_scripts')
    <script type="text/javascript">
        window.__UploaderIndex = '{{ $index }}';

        function initTooltip() {
            $('.tooltip').tooltipster({
                theme: 'tooltipster-punk'
            });
        }

        $(function () {
            var popup, container, url, model, field, path, type, accept, keyword, timer, cropper, style;

            var page = 2,
                token = '{{ csrf_token() }}';

            var uploadLoad = function (_this) {
                popup = _this.next('.upload-new');
                container = popup.find('.modal-tab.active div.uploads');
                url = '{{ route('admin.uploads.get') }}';
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
            }, uploadScroll = function (_this) {
                popup = _this.closest('.upload-new');
                container = popup.find('.modal-tab.active div.uploads');
                url = '{{ route('admin.uploads.get') }}';
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
            }, uploadSearch = function (_this) {
                popup = _this.closest('.upload-new');
                container = popup.find('.modal-tab.active div.uploads');
                url = '{{ route('admin.uploads.get') }}';
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
            }, uploadUpload = function (_this) {
                popup = _this.closest('.upload-new');
                accept = popup.find('ul.modal-tabs li.active').data('accept');

                _this.fileupload({
                    url: '{{ route('admin.uploads.upload') }}',
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
            }, uploadSave = function (_this) {
                popup = _this.closest('.upload-new');
                container = popup.find('.modal-tab.active div.uploads');
                url = "{{ URL::route('admin.uploads.set') }}";
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
                        var input = popup.closest('.field-wrapper').next('.upload-input'),
                            button = popup.prev('a.open-upload-new'),
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
            }, uploadCrop = function (_this) {
                popup = _this.closest('.upload-current');
                model = popup.data('model');
                field = popup.data('field');
                url = _this.data('url');
                path = _this.data('path');
                style = _this.data('style');

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.uploads.crop') }}',
                    dataType: 'json',
                    data: {
                        _token : token,
                        index: window.__UploaderIndex,
                        model: model,
                        field: field,
                        url: url,
                        path: path,
                        style: style
                    },
                    success: function(data) {
                        if (data.status === true) {
                            popup.next('.upload-crop-container').html(data.html);
                            popup.next('.upload-crop-container').find('.upload-crop').show();

                            /*$('#upload-crop-container-' + window.__UploaderIndex).html(data.html);
                            $('#upload-crop-' + window.__UploaderIndex).show();*/
                        }
                    }
                });
            };

            //initial load
            $(document).on('click', '.open-upload-new:not(.disabled)', function (e) {
                e.preventDefault();

                uploadLoad($(this));
            });

            //click load
            $(document).on('click', '.upload-new:not(.disabled) ul.modal-tabs > li', function(e) {
                e.preventDefault();

                uploadLoad($(this));
            });

            //scroll load
            document.addEventListener('scroll', function (event) {
                if (event.target.classList.contains('uploads')) {
                    uploadScroll($(event.target));
                }
            }, true);

            //search load
            $(document).on('keyup', '.upload-new:not(.disabled) .modal-tab.active input.search', function(e) {
                e.preventDefault();

                uploadSearch($(this));
            });

            //upload new
            $(document).on('click', '.upload-new:not(.disabled) label.upload-btn > input[type="file"]', function (e) {
                uploadUpload($(this));
            });

            //save new
            $(document).on('click', '.upload-new:not(.disabled) .upload-save', function(e) {
                e.preventDefault();

                uploadSave($(this));
            });

            //cropper load
            $(document).on('click', '.open-upload-cropper:not(.disabled)', function (e) {
                e.preventDefault();

                uploadCrop($(this));
            });

            //delete current
            $(document).on('click', '.upload-current:not(.disabled) .upload-delete', function(){
                $('#upload-input-' + window.__UploaderIndex).val('');
                $('#open-upload-current-' + window.__UploaderIndex).remove();
                $('#open-upload-new-' + window.__UploaderIndex).removeClass('half').addClass('full');
                $('.popup:visible').hide();
            });
        });
    </script>
@append