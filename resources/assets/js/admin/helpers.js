var init = {
    UploadManager: function (exists, container, oldIndex, newIndex) {
        if (exists) {
            container.find('section.upload-new, section.upload-current').each(function (index, _container) {
                if ($(_container).attr('data-field')) {
                    $(_container).attr('data-field', $(_container).attr('data-field').replace(oldIndex, newIndex));
                }
            });

            container.find('section.upload-input').each(function (index, _container) {
                if ($(_container).attr('name')) {
                    $(_container).attr('name', $(_container).attr('name').replace(oldIndex, newIndex));
                }
            });
        } else {
            container.find('a.open-upload-new').each(function (i, _container) {
                $(_container).removeClass('half').addClass('full');
            });

            container.find('a.open-upload-current, section.upload-current').each(function (i, _container) {
                $(_container).remove();
            });

            container.find('section.upload-new').each(function (i, _container) {
                $(_container).attr('data-field', $(_container).attr('data-field').replace(/[0-9]+/g, newIndex));
            });

            container.find('input.upload-input').each(function (i, _container) {
                $(_container).attr('name', $(_container).attr('name').replace(/[0-9]+/g, newIndex)).val('');
            });
        }
    },
    FlashMessage: function (type, message) {
        var flash = $('div.flash');

        flash.removeClass('success error warning').addClass(type);
        flash.find('p').text('').text(message);
        flash.fadeIn(800);

        setTimeout(function(){
            flash.fadeOut(800);
        }, 10000);
    },
    Editor: function () {
        tinymce.init({
            selector: "textarea.editor-input",
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            height: 300
        });
    },
    Chosen: function () {
        var select = $('.select-input'),
            width = '80%';

        if ($(window).width() < 1248) {
            width = '73%';
        }

        if ($(window).width() < 640) {
            width = '100%';
        }

        select.chosen({
            width: width,
            no_results_text: "Nothing found for",
            allow_single_deselect: true
        });
    },
    DatePicker: function () {
        var date = $('.date-input');

        date.datepicker({
            dateFormat: 'yy-mm-dd'
        });
    },
    TimePicker: function () {
        var time = $('.time-input');

        time.timepicker({
            showLeadingZero: true,
            showPeriodLabels: false,
            defaultTime: '00:00'
        });
    },
    ColorPicker: function () {
        var color = $('.color-input');

        color.ColorPicker({
            color: '#ffffff',
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val('#' + hex);
                $(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            }
        }).bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
        });
    },
    Tooltip: function () {
        var tooltip = $('.tooltip');

        tooltip.tooltipster({
            theme: 'tooltipster-punk'
        });
    }
};

var query = {
    params: function () {
        var vars = [], hash;
        var hashes = window.location.search ?
            window.location.href.slice(window.location.href.indexOf('?') + 1).split('&') :
            null;

        if (!hashes) {
            return [];
        }

        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');

            vars.push({
                name: hash[0],
                value: hash[1]
            });
        }

        return vars;
    },
    param: function (name) {
        name = name.replace(/[\[\]]/g, "\\$&");

        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(window.location.href);

        if (!results) {
            return null;
        }

        if (!results[2]) {
            return '';
        }

        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
};

var clipboard = {
    copy: function (text) {
        var target, focus, targetId;

        targetId = "_hiddenCopyText_";
        target = document.getElementById(targetId);

        if (!target) {
            target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;

            document.body.appendChild(target);
        }

        target.textContent = text;
        focus = document.activeElement;

        target.focus();
        target.setSelectionRange(0, target.value.length);

        var succeed;

        try {
            succeed = document.execCommand("copy");
        } catch (e) {
            succeed = false;
        }

        if (focus && typeof focus.focus === "function") {
            focus.focus();
        }

        target.textContent = "";

        return succeed;
    }
};