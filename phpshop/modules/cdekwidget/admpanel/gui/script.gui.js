function cdekvalidate(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault)
            theEvent.preventDefault();
    }
}

function cdekAdminWidgetOnChoose(result)
{
    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/cdekwidget/ajax/ajax.php',
        type: 'post',
        data: {
            operation: 'changeAddress',
            type: $('input[name="cdek_type"]').val(),
            city: result['city'],
            pvz: result['id'],
            tariff: result['tarif'],
            cost: result['price'],
            info: $('input[name="cdekInfo"]').val(),
            orderId: $('input[name="cdek_order_id"]').val()
        },
        dataType: "json",
        async: false,
        success: function (json) {
            if (json['success']) {
                
                /*
                if (Number($.getUrlVar('tab')) !== 4) {
                    window.location.href += '&tab=4';
                } else {
                    location.reload();
                }*/
                
               location.reload();
                
                
            } else {
                console.log(json['error'])
            }
        }
    });
}

$(document).ready(function () {
    if (Number($.getUrlVar('tab')) === 4) {
        $('a[href="#tabs-4"]').tab('show');
    }

    if (typeof $('#body').attr('data-token') !== 'undefined' && $('#body').attr('data-token').length)
        var DADATA_TOKEN = $('#body').attr('data-token');
    if (DADATA_TOKEN !== false && DADATA_TOKEN !== undefined) {
        $("input[name='city_from_new']").suggestions({
            token: DADATA_TOKEN,
            type: 'ADDRESS',
            hint: false,
            bounds: "city-settlement",
            onSelect: function (response) {
                $("input[name='city_from_new']").val(response.data.city)
            }
        });
        $("input[name='default_city_new']").suggestions({
            token: DADATA_TOKEN,
            type: 'ADDRESS',
            hint: false,
            bounds: "city-settlement",
            onSelect: function (response) {
                $("input[name='default_city_new']").val(response.data.city)
            }
        });
    }

    $('.cdek-change-address').on('click', function () {
        
        cdekwidgetStart();
    });

    $('.cdek-send').on('click', function () {
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '/phpshop/modules/cdekwidget/ajax/ajax.php',
            type: 'post',
            data: {operation: 'send', orderId: $('input[name="cdek_order_id"]').val()},
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

    // Изменение статуса оплаты
    $('#payment_status').on('change', function () {
        var paymentStatus = 0;
        if ($(this).prop('checked') === true) {
            paymentStatus = 1;
        }
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '/phpshop/modules/cdekwidget/ajax/ajax.php',
            type: 'post',
            data: {operation: 'paymentStatus', value: paymentStatus, orderId: $('input[name="cdek_order_id"]').val()},
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success']) {

                    location.reload();

                } else {
                    console.log(json['error']);
                }
            }

        });
    });
});