function loader() {
    return "<img src='/../images/loading-sm.gif'>"
}

function setSortableList(selector) {
    var options = {
        placeholderCss: {'background-color': '#ff8'},
        hintCss: {'background-color': '#bbf'},
        isAllowed: function (cEl, hint, target) {
            if (target.data('module') === 'c' && cEl.data('module') !== 'c') {
                hint.css('background-color', '#ff9999');
                return false;
            } else {
                hint.css('background-color', '#99ff99');
                return true;
            }
        },
        opener: {
            active: true,
            as: 'html',
            close: '<span class="glyphicon glyphicon-minus c3"></span>',
            open: '<span class="glyphicon glyphicon-plus"></span>',
            openerCss: {}
        },
        ignoreClass: 'clickable'
    };

    var optionsPlus = {
        insertZonePlus: true,
        placeholderCss: {'background-color': '#ff8'},
        hintCss: {'background-color': '#bbf'},
        opener: {
            active: true,
            as: 'html',  // if as is not set plugin uses background image
            close: '<span class="glyphicon glyphicon-minus c3"></span>',
            open: '<span class="glyphicon glyphicon-plus"></span>',
            openerCss: {}
        }
    };
    selector.sortableLists(options);
}

function saveTree(selector, treeSelector, url, tableName) {
    selector.on('click', function (event) {
        event.preventDefault()
        let tree = treeSelector.sortableListsToArray()
        let loaderArea = $(this).parent().find('.tree-loader-area')
        loaderArea.addClass('tree-loader')
        $.ajax({
            url: url,
            data: {
                'tree': tree,
                'tableName': tableName,
            },
            type: 'post',
        }).done(function() {
            loaderArea.removeClass('tree-loader')
        });

    })
}

$(function () {
    $('span.tree-action-column a').on('mousedown', function (event) {
        return false;
    })
})
