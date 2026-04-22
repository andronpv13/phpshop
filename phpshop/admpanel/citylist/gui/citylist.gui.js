$(document).ready(function() {


    // Управление деревом категорий
    if (typeof(TREEGRID_LOAD) != 'undefined')
        $('.title-icon .glyphicon-chevron-down').on('click', function() {
            $('.tree').treegrid('expandAll');
        });

    if (typeof(TREEGRID_LOAD) != 'undefined')
        $('.title-icon .glyphicon-chevron-up').on('click', function() {
            $('.tree').treegrid('collapseAll');
        });

    // Дерево категорий
    if (typeof(TREEGRID_LOAD) != 'undefined')
        $('.tree').treegrid({
            saveState: true,
            expanderExpandedClass: 'glyphicon glyphicon-triangle-bottom',
            expanderCollapsedClass: 'glyphicon glyphicon-triangle-right'
        });

    $('.data-tree .dropdown-toggle').addClass('btn-xs');

    // Раскрытие категорий
    if (typeof(TREEGRID_LOAD) != 'undefined')
        $(".treegrid-parent").on('click', function(event) {
            event.preventDefault();
            $('.' + $(this).attr('data-parent')).treegrid('toggle');
        });

    // Редактировать категорию в дереве
    $(".tree .edit").on('click', function(event) {
        event.preventDefault();
        window.location.href += '&id=' + $(this).attr('data-id');
    });

    // Удалить категорию в дереве
    $(".tree .delete").on('click', function(event) {
        event.preventDefault();
        var id = $(this).closest('.data-tree');
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function() {

            $('.list_edit_' + data_id).ajaxSubmit({
                success: function() {
                    id.empty();
                    showAlertMessage(locale.save_done);
                }
            });
        })

    });

    // Активация из списка dropdown
    $('.data-row, .data-tree').hover(
        function() {
            $(this).find('#dropdown_action').show();
            $(this).find('.editable').removeClass('input-hidden');
            $(this).find('.media-object').addClass('image-shadow');
        },
        function() {
            $(this).find('#dropdown_action').hide();
            $(this).find('.editable').addClass('input-hidden');
            $(this).find('.media-object').removeClass('image-shadow');
        });

    // Удаление из карточки
    $(".deleteRegion").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            $('#product_edit').append('<input type="hidden" name="delID" value="1">');
            $('#product_edit').append('<input type="hidden" name="ajax" value="1">');
            $('#product_edit').ajaxSubmit({
                dataType: "json",
                success: function (json) {

                    if (json['success'] == 1) {
                        window.location.href = '?path=citylist';
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })
    });
});