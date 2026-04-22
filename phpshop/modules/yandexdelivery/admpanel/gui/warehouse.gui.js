
function yandexWidgetOnChoose(result) {
    $('input[name="warehouse_id_new"]').val(result.id);
    $("#yandexwidgetModal").modal("hide");
}

document.addEventListener('YaNddWidgetPointSelected', function (data) {

    var warehouse = data.detail.id;

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/yandexdelivery/ajax/admin.php',
        type: 'post',
        data: {
            operation: 'changeWarehouse',
            value: warehouse,

        },
        dataType: "json",
        async: false,
        success: function (json) {
            if (json['success']) {
                location.reload();
            } else {
                console.log(json['error'])
            }
        }
    });


});

$(document).ready(function () {

    $('#yandexdelivery-select-warehouse').on('click', function (event) {
        event.preventDefault();

        YaDelivery.setParams({

            filter: {
                // Тип способа доставки
                type: [
                    "pickup_point", // Пункт выдачи заказа
                ],
                is_yandex_branded: true
            }
        })

        $('#yandexwidgetModal').modal('toggle');
    });

});