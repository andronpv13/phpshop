function pochtaInit(type) {

    $("#makeyourchoise").val(null);

    $('input[name="pochta_cost"]').remove();
    $('input[name="pochta_address"]').remove();
    $('input[name="pochta_type"]').remove();
    $('input[name="pochta_city"]').remove();
    $('input[name="pochta_index"]').remove();
    $('input[name="pochta_mail_type"]').remove();
    $('input[name="pochta_region"]').remove();
    $('input[name="pochta_delivery_info"]').remove();
    $('input[name="pochta_pvz_type"]').remove();

    $('<input type="hidden" name="pochta_cost">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_address">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_city">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_index">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_mail_type">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_region">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_delivery_info">').insertAfter('#dop_info');
    $('<input type="hidden" name="pochta_pvz_type">').insertAfter('#dop_info');

    $('#pochta-frame').html('');
    $("#pochtaModal").modal("show");

    if(type === 'pvz') {
        $('<input type="hidden" name="pochta_type" value="pvz">').insertAfter('#dop_info');
        console.log($('input[name="pochta_order_lines"]').val());
        ecomStartWidget({
            id: $('input[name="pochta_widget_id"]').val(),
            weight: $('input[name="pochta_weight"]').val(),
            sumoc: $('input[name="pochta_ins_value"]').val(),
            order_lines: $.parseJSON($('input[name="pochta_order_lines"]').val()),
            callbackFunction: pochtaCallback,
            containerId: 'pochta-frame'
        });
    } else {
        $('<input type="hidden" name="pochta_type" value="courier">').insertAfter('#dop_info');
        courierStartWidget({
            id: $('input[name="pochta_courier_widget_id"]').val(),
            weight: $('input[name="pochta_weight"]').val(),
            sumoc: $('input[name="pochta_ins_value"]').val(),
            callbackFunction: pochtaCallbackCourier,
            containerId: 'pochta-frame'
        });
    }
}

function pochtaCallback(result) {

    var message = locale.pochta.pickup+': ' + result.indexTo + ', ' + result.deliveryDescription.description;
    $('#deliveryInfo').html(message);
    $('input[name="pochta_delivery_info"]').val(message);

    pochtaSetData(result);
}

function pochtaCallbackCourier(result) {
    if(!result.hasOwnProperty('cashOfDelivery')) {
        if(typeof showAlertMessage === "function"){
            showAlertMessage('Ошибка тарификации. Проверьте правильность введенного индекса', true);
        } else {
            alert('Ошибка тарификации. Проверьте правильность введенного индекса');
        }
        return;
    }

    var message = 'Почта России, курьерская доставка: ' + result.cityTo + ', ' + result.addressTo + ', ' + result.delivery.description;
    $('#deliveryInfo').html(message);
    $('input[name="pochta_delivery_info"]').val(message);

    pochtaSetData(result);
}

function pochtaSetData(result) {
    var region = result.regionTo;
    if(!region) {
        region = result.areaTo; // В ручном вводе адреса курьерской доставки область\регион в поле района
    }

    var deliveryCost = Number(result.cashOfDelivery / 100);
    deliveryCost = Math.round(deliveryCost);
    if($("#d").data('free') === 1) {
        deliveryCost = 0;
    }

    $("#makeyourchoise").val('DONE');

    $("#DosSumma").html(deliveryCost);
    $("#TotalSumma").html(Number(deliveryCost) + Number($('#OrderSumma').val())- Number($('#SkiSumma').attr('data-discount')));

    $('input[name="pochta_cost"]').val(deliveryCost);
    $('input[name="pochta_address"]').val(result.addressTo);
    $('input[name="pochta_city"]').val(result.cityTo);
    $('input[name="pochta_index"]').val(result.indexTo);
    $('input[name="pochta_mail_type"]').val(result.mailType);
    $('input[name="pochta_region"]').val(region);
    $('input[name="pochta_pvz_type"]').val(result.pvzType);
    $('#dop_info').val(result.comment);

    if($('input[name="state_new"]').length) {
        $('input[name="state_new"]').val(region);
    }
    if($('input[name="city_new"]').length) {
        $('input[name="city_new"]').val(result.cityTo);
    }
    if($('input[name="index_new"]').length) {
        $('input[name="index_new"]').val(result.indexTo);
    }

    $("#pochtaModal").modal("hide");
}