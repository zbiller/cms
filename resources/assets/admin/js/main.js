$(function () {
    flash();
    menu();
    buttons();
    filter();
    sort();
    tabs();
    setups();
});

/**
 * @return void
 */
function flash() {
    var flash = $('div.flash');

    flash.find('a').click(function () {
        flash.fadeOut(800);
    });

    if (flash.length > 0) {
        setTimeout(function(){
            flash.fadeOut(800);
        }, 10000);
    }
}

/**
 * @return void
 */
function menu() {
    var menu = $('nav'),
        mainMenu = $('ul.main-menu'),
        subMenu = $('ul.sub-menu'),
        mobileMenuButton = $('header > button');

    //scrollable content
    menu.nanoScroller({
        scroll: 'top',
        contentClass: 'scroll'
    });

    //sub-menu expand
    mainMenu.find('li > a').click(function (e) {
        mainMenu.find('li > a').removeClass('active');
        subMenu.slideUp();

        if ($(this).next().css('display') == 'none') {
            $(this).next().slideDown();
            $(this).addClass('active');
        } else {
            $(this).next().slideUp();
            $(this).removeClass('active');
        }
    });

    //mobile
    mobileMenuButton.click(function(){
        menu.animate({
            "margin-left": menu.css('margin-left') == '-250px' ? '0px' : '-250px'
        }, 300);
    });
}

/**
 * @return void
 */
function buttons () {
    var updateButton = $('a.update');

    //update button
    updateButton.click(function () {
        location.reload();
    })
}

/**
 * @return void
 */
function filter() {
    var filterContainer = $('section.filters'),
        filterForm = $('section.filters > form'),
        filterButton = $('section.filters a.filter'),
        clearButton = $('section.filters a.clear');

    //trigger filter submit
    filterButton.click(function () {
        filterForm.submit();
    });

    //clear filters
    clearButton.click(function () {
        window.location.href = window.location.href.split('?')[0];
    });
}

/**
 * @return void
 */
function sort() {
    var sortField = $('section.list > table > thead > tr > td.sortable');

    //initialize sort headings display
    sortField.each(function () {
        if (_getParam('sort') == $(this).data('sort')) {
            if (_getParam('dir') == 'asc') {
                $(this).attr('data-dir', 'desc');
                $(this).find('i').addClass('fa-sort-asc');
            } else {
                $(this).attr('data-dir', 'asc');
                $(this).find('i').addClass('fa-sort-desc');
            }
        }

        if (!$(this).attr('data-dir')) {
            $(this).attr('data-dir', 'asc');
        }
    });


    //create sort full url & redirect
    sortField.click(function () {
        var url = window.location.href.replace('#', '').split('?')[0],
            params = [];

        $.each(_getParams(), function (index, obj) {
            if (obj.name == 'sort' || obj.name == 'dir') {
                return true;
            }

            params.push(obj);
        });

        params.push({
            name: 'sort',
            value: $(this).data('sort')
        });

        params.push({
            name: 'dir',
            value: $(this).data('dir') ? $(this).data('dir') : 'asc'
        });

        window.location.href = url + '?' + decodeURIComponent($.param(params));
    });
}

/**
 * @return void
 */
function tabs() {
    var hash = window.location.hash,
        button = $('section.tabs > a'),
        container = $('div.tab');

    //display the correct tab on load
    if (hash) {
        var currentButton = $('section.tabs > a[href="'+ hash +'"]'),
            currentContainer = $(hash);

        button.removeClass('active');
        container.removeClass('active');

        currentButton.addClass('active');
        currentContainer.addClass('active');
    } else {
        button.first().addClass('active');
        container.first().addClass('active');
    }

    //display the correct tab on click
    button.click(function () {
        button.removeClass('active');
        container.removeClass('active');

        $(this).addClass('active');
        $($(this).attr('href')).addClass('active');
    });
}

/**
 * @return void
 */
function setups()
{
    //TinyMCE setup
    tinymce.init({
        selector: "textarea.editor",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        height: 300
    });

    //Chosen setup
    var chosen = $('.chosen-select'),
        width = '80%';

    if ($(window).width() < 1248) {
        width = '73%';
    }

    if ($(window).width() < 640) {
        width = '100%';
    }

    chosen.chosen({
        width: width,
        no_results_text: "Nothing found for",
        allow_single_deselect: true
    });

    //DatePicker setup
    var date = $('.date-input');

    date.datepicker({
        dateFormat: 'dd M, yy'
    });

    //TimePicker setup
    var time = $('.time-input');

    time.timepicker({
        showLeadingZero: true,
        showPeriodLabels: false,
        defaultTime: '00:00'
    });

    //ColorPicker setup
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
}

/**
 * @returns {Array}
 * @private
 */
function _getParams()
{
    var vars = [], hash;
    var hashes = window.location.search ?
        window.location.href.slice(window.location.href.indexOf('?') + 1).split('&') :
        null;

    if (!hashes) {
        return [];
    }

    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        //vars.push(hash[0]);

        vars.push({
            name: hash[0],
            value: hash[1]
        });

        //vars[hash[0]] = hash[1];
    }

    return vars;
}

/**
 * @param name
 * @returns {*}
 * @private
 */
function _getParam(name) {
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