$(window).load(function(){
    /**
     * General Scripts
     */
    //load();
    flash();
    menu();
    buttons();
    inputs();
    sort();
    order();
    tabs();
    pagination();
    popups();
    sluggify();
    setups();
    helpers();

    /**
     * Custom Scripts
     */
    tree();
    menus();
    blocks();
});

/**
 * @return void
 */
function load()
{
    $('#loading').fadeOut();
    $('header, nav, main, footer').fadeIn();
}

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
function buttons() {
    //save
    $(document).on('click', 'a.btn-save-record', function (e) {
        e.preventDefault();

        $('form.form').submit();
    });

    //save stay
    $(document).on('click', 'a.btn-save-stay', function (e) {
        e.preventDefault();

        $('form.form').append('<input type="hidden" name="save_stay" value="1" />').submit();
    });

    //save elsewhere
    $(document).on('click', 'a.btn-save-elsewhere', function (e) {
        e.preventDefault();

        $('form.form').attr('action', $(this).data('url')).submit();
    });

    //save new
    $(document).on('click', 'a.btn-save-new', function (e) {
        e.preventDefault();

        $('form.form').attr('action', $(this).data('url')).submit();
    });

    //save draft
    $(document).on('click', 'a.btn-save-draft', function (e) {
        e.preventDefault();

        $('form.form').attr('action', $(this).data('url')).submit();
    });

    //publish
    $(document).on('click', 'a.btn-publish-draft', function (e) {
        e.preventDefault();

        if (confirm('Are you sure you want to publish this draft?')) {
            $('form.form').attr('action', $(this).data('url')).submit();
        }

        return false;
    });

    //preview
    $(document).on('click', 'a.btn-preview-record', function (e) {
        e.preventDefault();

        $('form.form').attr('action', $(this).data('url')).attr('target', '_blank').submit();
    });

    //filter
    $(document).on('click', 'a.btn-filter-records', function (e) {
        e.preventDefault();

        $(this).closest('section.filters').find('form').submit();
    });

    //clear
    $(document).on('click', 'a.btn-clear-filters', function (e) {
        e.preventDefault();

        window.location.href = window.location.href.split('?')[0];
    });

    //update
    $(document).on('click', 'a.btn-update-page', function (e) {
        e.preventDefault();

        location.reload();
    });

    //submit
    $(document).on('click', 'button.btn-submit', function (e) {
        e.preventDefault();

        var shouldConfirm = $(this).data('confirm') ? true : false;

        if (!shouldConfirm || (shouldConfirm && confirm('{{ $confirm }}'))) {
            $('form:first-of-type').attr('action', $(this).data('url')).submit();
        }

        return false;
    });
}

/**
 * @return void
 */
function inputs() {
    //set file input display file name
    $('label.file-input > input[type="file"]').change(function (){
        $(this).next().html($(this).val().split('\\').pop());
    });
}

/**
 * @return void
 */
function sort() {
    var sortField = $('section.list > table > thead > tr > td.sortable');

    //initialize sort headings display
    sortField.each(function () {
        if (query.param('sort') == $(this).data('sort')) {
            if (query.param('dir') == 'asc') {
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

        $.each(query.params(), function (index, obj) {
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
function order()
{
    $('table[data-orderable="true"]').tableDnD({
        onDrop: function(table, row){
            var rows = table.tBodies[0].rows,
                items = {};

            for (var i = 0; i < rows.length; i++) {
                items[i + 1] = rows[i].id;
            }

            $.ajax({
                type: 'PATCH',
                url: $(table).data('order-url'),
                data: {
                    _method: 'PATCH',
                    _token: $(table).data('order-token'),
                    model: $(table).data('order-model'),
                    items: items
                }
            });
        }
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
    button.click(function (e) {
        e.preventDefault();

        button.removeClass('active');
        container.removeClass('active');

        $(this).addClass('active');
        $($(this).attr('href')).addClass('active');
    });
}

/**
 * @return void
 */
function pagination() {
    //disable click on inactive buttons
    $('section.pagination a.inactive').click(function (e) {
        e.preventDefault();
    });
}

/**
 * @return void
 */
function popups()
{
    var tabButtons = $('section.popup ul.modal-tabs > li');

    //open
    $(document).on('click', 'a[data-popup="open"]', function (e) {
        e.preventDefault();

        showSelectedPopup($(this));
    });

    //close
    $(document).on('click', 'section.popup:visible a[data-popup="close"]', function (e) {
        e.preventDefault();

        resetVisiblePopup($(this));
        hideVisiblePopup($(this));
    });

    //tabs
    tabButtons.click(function (e) {
        e.preventDefault();

        hideVisiblePopupTabs();
        showSelectedPopupTab($(this))
    });

    //upload select
    $('body').on('click', 'section.popup:visible div.uploads > a', function (e) {
        e.preventDefault();

        $('section.popup:visible div.uploads > a').removeClass('active');
        $(this).addClass('active');
    });

    /**
     * Helper functions.
     */
    var showSelectedPopup = function (_this) {
            $('#' + _this.data('popup-id')).show();
        },
        hideVisiblePopup = function (_this) {
            _this.closest('section.popup:visible').hide();
        },
        resetVisiblePopup = function (_this) {
            _this.closest('section.popup:visible').find('ul.modal-tabs > li').removeClass('active');
            _this.closest('section.popup:visible').find('ul.modal-tabs > li:first-of-type').addClass('active');

            _this.closest('section.popup:visible').find('div.modal-tab').removeClass('active');
            _this.closest('section.popup:visible').find('div.modal-tab:first-of-type').addClass('active');
        },
        showSelectedPopupTab = function (_this) {
            _this.addClass('active');
            $('section.popup:visible ' + _this.find('a').attr('href')).addClass('active');
        },
        hideVisiblePopupTabs = function () {
            $('section.popup:visible ul.modal-tabs > li').removeClass('active');
            $('section.popup:visible div.modal-tab').removeClass('active');
        };
}

/**
 * @return void
 */
function sluggify()
{
    var from = $('#slug-from');
    var to = $('#slug-to');

    if (from.length && to.length) {
        from.bind('keyup blur', function() {
            to.val(
                $(this).val().toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '')
            );
        });
    }
}

/**
 * @return void
 */
function setups() {
    init.Editor();
    init.Chosen();
    init.DatePicker();
    init.TimePicker();
    init.ColorPicker();
    init.Tooltip();
}

/**
 * @return void
 */
function helpers()
{
    //check/uncheck all checkboxes
    $('input[type="checkbox"][name="dummy"]').click(function () {
        var column = $(this).closest('td').parent().children().index($(this).closest('td'));
        var checkboxes = $('table > tbody td:nth-child(' + (column + 1) + ') input[type="checkbox"]');

        checkboxes.prop('checked', $(this).prop('checked'));
    });

    //generate password
    $('#password-generate').pGenerator({
        'bind': 'click',
        'passwordElement': 'input[name="password"]',
        'passwordLength': 10,
        'uppercase': true,
        'lowercase': true,
        'numbers':   true,
        'specialChars': true,
        'onPasswordGenerated': function(generatedPassword) {
            clipboard.copy(generatedPassword);

            $('input[type="password"][name="password_confirmation"]').val(generatedPassword);
            alert('The generated password has been copied to your clipboard.');
        }
    });
}

/**
 * @return void
 */
function tree()
{
    var tree = $(".jstree");

    var load = function (tree) {
        setTimeout(function () {
            tree.jstree({
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
                            return tree.data('load-url') + (parseInt(node.id) ? "/" + node.id : '');
                        }
                    }
                },
                "state" : { "key" : "state_key" },
                "plugins" : ["dnd", "state", "types"]
            });
        }, 500);
    };

    var list = function (tree) {
        tree.on('select_node.jstree', function (e, data) {
            var node = data.instance.get_node(data.selected);
            var request = {};

            $(tree.data('table')).css({opacity: 0.5});
            $('a.btn.btn-add-record').attr('href', tree.data('add-url'));

            $.each(query.params(), function (index, obj) {
                request[obj.name] = obj.value;
            });

            $.ajax({
                url: tree.data('list-url') + "/" + (parseInt(node.id) ? node.id : ''),
                type: 'GET',
                data: request,
                success: function(data){
                    $('a.btn.btn-add-record').attr('href', $('a.btn.btn-add-record').attr('href') + '/' + (parseInt(node.id) ? node.id : ''));
                    $(tree.data('container')).html(data);
                    $(tree.data('table')).css({opacity: 1});

                    //init sorting
                    sort();
                }
            });
        });
    };

    var move = function (tree) {
        tree.on('move_node.jstree', function (e, data) {
            var _tree = tree.jstree().get_json();
            var _node = data.node;
            var _data = {
                page: parseInt(_node.id) ? _node.id : '',
                children: _node.children,
                parent: parseInt(data.parent) ? data.parent : '',
                old_parent: parseInt(data.old_parent) ? data.old_parent : ''
            };

            $.ajax({
                url: tree.data('move-url'),
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: tree.data('token'),
                    tree: _tree
                },
                success: function(data) {
                    if (data > 0 && tree.data('has-url') === true) {
                        $.ajax({
                            url: tree.data('url-url'),
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                _token: tree.data('token'),
                                data: _data
                            },
                            success: function(data) {

                            }
                        });
                    }
                }
            });
        });
    };

    load(tree);
    list(tree);
    move(tree);
}

/**
 * @return void
 */
function menus()
{
    var menuTypeSelect = '#menu-type-select';
    var selectMenuType = function (type) {
        $('div.menu-types').hide();

        if (type.val() == type.data('default-type')) {
            $('#menu-type-default').show();
        } else {
            var chosen = $('select[name="menuable_id"]');

            chosen.empty();

            $.ajax({
                type: 'GET',
                url: type.data('url') + '/' + type.val(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        $.each(data.attributes, function (index, attribute){
                            chosen.append(
                                '<option value="' + attribute.value + '"' + (
                                    attribute.value == type.data('selected') ? ' selected' : ''
                                ) + '>' + attribute.name + '</option>'
                            ).trigger('chosen:updated');
                        });
                    }

                    $('#menu-type-custom').show();
                }
            });
        }
    };

    if ($(menuTypeSelect).val() != '') {
        selectMenuType($(menuTypeSelect));
    }

    $(document).on('change', menuTypeSelect, function () {
        selectMenuType($(this));
    });
}

/**
 * @return void
 */
function blocks()
{
    //select block type on add
    if ($('#block-type').length) {
        var type = $('#block-type'),
            button = $('#block-continue-button'),
            image = $('#block-image'),
            src;

        var selectType = function () {
            if (type.val()) {
                src = type.data('images')[type.val()];

                button.attr('href', type.data('url') + '/' + type.val());

                if (src) {
                    image.attr('src', type.data('image') + '/' + src).show();
                }
            } else {
                button.attr('href', '#');
                image.attr('src', '').hide();
            }
        };

        selectType();

        type.change(function () {
            selectType();
        });
    }

    //manage multiple items block type
    if ($('#block-items-template').length && $('#block-items-container').length) {
        var template = $('#block-items-template');
        var container = $('#block-items-container');

        var addBlockItem = function (index, data) {
            var item, text, current;

            if (!index) {
                item = container.find('.block-item:last');
                index = item.length ? parseInt(item.attr('data-index')) + 1 : 0;
            }

            text = template.html().replace(/#index/g, index);

            for (var i in data) {
                text = text.replace(new RegExp('#' + i + '#', 'g'), data[i] ? data[i] : '');
            }

            if (!data || !data.length) {
                text = text.replace(/#[a-z0-9_]+#/g, '');
            }

            container.append(text);

            current = container.find('.block-item[data-index="' + index + '"]');

            current.find('select').each(function (index, select) {
                if ($(select).data('selected')) {
                    $(select).find('option[value="' + $(select).data('selected') + '"]').attr('selected', true);
                }
            });

            init.UploadManager(false, current, null, index);

            setTimeout(function () {
                init.Editor();
                init.Chosen();
                init.DatePicker();
                init.TimePicker();
                init.ColorPicker();
                init.Tooltip();
            }, 500);
        };

        var deleteBlockItem = function (item) {
            var oldIndex, newIndex;

            item.nextAll('.block-item').each(function (index, selector) {
                oldIndex = parseInt($(selector).attr('data-index'));
                newIndex = parseInt($(selector).attr('data-index')) - 1;

                $(selector).attr('data-index', newIndex);
                $(selector).find('input, select, textarea').each(function (index, field) {
                    if ($(field).attr('name') != undefined) {
                        $(field).attr('name', $(field).attr('name').replace(oldIndex, newIndex));
                    }
                });

                init.UploadManager(true, $(selector), oldIndex, newIndex);
            });

            item.remove();
        };

        var moveBlockItem = function (item, direction) {
            var currentItem, previousItem, nextItem;
            var currentIndex, previousIndex, nextIndex;

            currentItem = item;
            currentIndex = currentItem.attr('data-index');

            if (item.prev().length) {
                previousItem = item.prev();
                previousIndex = previousItem.attr('data-index');
            }

            if (item.next().length) {
                nextItem = item.next();
                nextIndex = nextItem.attr('data-index');
            }

            if (direction == 'up' && previousItem && previousIndex) {
                previousItem.before(currentItem);

                currentItem.attr('data-index', previousIndex);
                previousItem.attr('data-index', currentIndex);

                currentItem.find('input, select, textarea').each(function (index, field) {
                    if ($(field).attr('name') != undefined) {
                        $(field).attr('name', $(field).attr('name').replace(currentIndex, previousIndex));
                    }
                });

                previousItem.find('input, select, textarea').each(function (index, field) {
                    if ($(field).attr('name') != undefined) {
                        $(field).attr('name', $(field).attr('name').replace(previousIndex, currentIndex));
                    }
                });

                init.UploadManager(true, currentItem, currentIndex, previousIndex);
                init.UploadManager(true, previousItem, previousIndex, currentIndex);
            }

            if (direction == 'down' && nextItem && nextIndex) {
                nextItem.after(currentItem);

                currentItem.attr('data-index', nextIndex);
                nextItem.attr('data-index', currentIndex);

                currentItem.find('input, select, textarea').each(function (index, field) {
                    if ($(field).attr('name') != undefined) {
                        $(field).attr('name', $(field).attr('name').replace(currentIndex, nextIndex));
                    }
                });

                nextItem.find('input, select, textarea').each(function (index, field) {
                    if ($(field).attr('name') != undefined) {
                        $(field).attr('name', $(field).attr('name').replace(nextIndex, currentIndex));
                    }
                });

                init.UploadManager(true, currentItem, currentIndex, nextIndex);
                init.UploadManager(true, nextItem, nextIndex, currentIndex);
            }
        };

        $(document).on('click', 'a#block-add-item', function (e) {
            e.preventDefault();
            addBlockItem();
        });

        $(document).on('click', 'a.block-delete-item', function (e) {
            e.preventDefault();
            deleteBlockItem($(this).closest('.block-item'));
        });

        $(document).on('click', 'a.block-move-item-up', function (e) {
            e.preventDefault();
            moveBlockItem($(this).closest('.block-item'), 'up');
        });

        $(document).on('click', 'a.block-move-item-down', function (e) {
            e.preventDefault();
            moveBlockItem($(this).closest('.block-item'), 'down');
        });
    }

    //assign blocks
    $('.block-assign-select').chosen({
        width: '100%',
        inherit_select_classes: true
    });
}