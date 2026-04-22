$(document).ready(function () {

    // Выделение текущей категории
    var cat = $.getUrlVar('cat');
    if (typeof cat == 'undefined') {
        cat = 0;
    }
    $('.treegrid-' + cat).addClass('treegrid-active');
    
    
    // Выбрать все категории
    $('body').on('change', '#categories_all', function () {
        if (this.checked)
            $('[name="categories[]"]').selectpicker('selectAll');
        else
            $('[name="categories[]"]').selectpicker('deselectAll');
    });

    // Автозаполнение дополнительных полей
    $('.autofill tr').each(function (key, value) {

        if (key > 0 && $(value).find(':nth-child(3) input:text').val() == "") {
            var def = $(value).find(':nth-child(1)').html();
            $(value).find(':nth-child(3) input:text').val(def);
            $(value).find(':nth-child(5) input:text').val(key);
        }
    });

    // Изменение данных из списка (цена, склад)
    $('.editable').on('change', function () {
        var data = [];
        data.push({name: $(this).attr('data-edit'), value: $(this).val()});
        data.push({name: 'rowID', value: $(this).attr('data-id')});
        data.push({name: 'saveID', value: 1});
        data.push({name: 'actionList[saveID]', value: 'actionSave'});
        data.push({name: 'ajax', value: 1});
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=delivery&id=' + $(this).attr('data-id'),
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == 1)
                    showAlertMessage(locale.save_done);
                else
                    showAlertMessage(locale.save_false, true);
            }
        });
    });

   
    // Создать новый из дерева
    $(".newcat").on('click', function (event) {
        event.preventDefault();
        window.location.href += '&action=new&target=cat';
    });

    // Активация из списка dropdown
    $('.data-row, .data-tree').hover(
            function () {
                $(this).find('#dropdown_action').show();
                $(this).find('.editable').removeClass('input-hidden');
                $(this).find('.media-object').addClass('image-shadow');
            },
            function () {
                $(this).find('#dropdown_action').hide();
                $(this).find('.editable').addClass('input-hidden');
                $(this).find('.media-object').removeClass('image-shadow');
            });

});