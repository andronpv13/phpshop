
function yandexWidgetOnChoose(result) {

    var info = locale.cdek.pickup + ': ' + result.id + ', ' + locale.cdek.pickup_address + ': ' + result.address;
    result.price = Math.ceil(result.price);

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/yandexdelivery/ajax/admin.php',
        type: 'post',
        data: {
            cost: result.price,
            operation: 'changeAddress',
            pvz: result.id,
            orderId: $('input[name="yadelivery_order_id"]').val(),
            info: info
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

    $("#yandexwidgetModal").modal("hide");
}

document.addEventListener('YaNddWidgetPointSelected', function (data) {


    $.ajax({
        url: '/phpshop/modules/yandexdelivery/ajax/price.php',
        type: 'post',
        data: {delivery_variant_id: data.detail.id, weight: $('input[name="yandexdelivery_weight"]').val()},
        success: function (approx_price) {

            yandexWidgetOnChoose({
                id: data.detail.id,
                address: data.detail.address.full_address,
                city: data.detail.address.geoId,
                cityName: data.detail.address.locality,
                tarif: '',
                price: approx_price
            });



        }
    });
});

$(document).ready(function () {

    // Изменение ПВЗ
    $('.yadelivery-change-address').on('click', function (event) {
        event.preventDefault();

        YaDelivery.setParams({
            show_select_button: false
        })

        $('#yandexwidgetModal').modal('toggle');
    });

    // Изменение статуса оплаты
    $('#yandex_payment_status').on('change', function () {
        var paymentStatus = 0;
        if ($(this).prop('checked') === true) {
            paymentStatus = 1;
        }

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '/phpshop/modules/yandexdelivery/ajax/admin.php',
            type: 'post',
            data: {
                operation: 'changePaymentStatus',
                value: paymentStatus,
                orderId: $('input[name="yadelivery_order_id"]').val()
            },
            dataType: "json",
            async: false,
            success: function (json) {}
        });



    });

    // Отправить заказ
    $('.yadelivery-send').on('click', function () {
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '/phpshop/modules/yandexdelivery/ajax/admin.php',
            type: 'post',
            data: {
                operation: 'send',
                orderId: $('input[name="yadelivery_order_id"]').val()
            },
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == false) {
                    $.MessageBox({
                        buttonDone: false,
                        buttonFail: locale.close,
                        input: false,
                        message: json['error']
                    })
                } else {
                    $.MessageBox({
                        buttonDone: false,
                        buttonFail: locale.close,
                        input: false,
                        message: 'Заказ успешно отправлен'
                    });
                    $('.yandex-status').html('Отправлен');
                    $('.yadelivery_actions').css('display', 'none');
                    $("#yadelivery-table input[type=checkbox]").attr('disabled', true);
                }
            }
        });
    });
});