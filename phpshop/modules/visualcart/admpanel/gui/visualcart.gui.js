$(document).ready(function () {

    // Создать заказ
    $("body").on('click', ".data-row .order", function (event) {
        event.preventDefault();
        var id = $(this).closest('.data-row');
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_create_order
        }).done(function () {
            var data = [];
            data.push({name: 'rowID', value: data_id});
            data.push({name: 'editID', value: '1'});
            data.push({name: 'actionList[editID]', value: 'actionUpdate.order.edit'});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=modules.dir.visualcart&id='+data_id,
                data: data,
                type: 'post',
                dataType: "json",
                async: false,
                success: function (json) {

                    if (json['success'] == 1) {
                        showAlertMessage(locale.save_done);
                        id.remove();

                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });

        });
    });

});