function pochtavalidate(evt) {
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

$(document).ready(function () {

    $('.pochta-change-address').on('click', function () {
        pochtaInit('pvz');
    });

    $('body').on('change', '#pochta_payment_status', function () {
        pochtaChangePaymentStatus($('#pochta_payment_status').prop('checked'));
    });

    $('body').on('change', '#pochta_completeness-checking', function () {
        pochtaChangeSettings($('#pochta_completeness-checking').prop('checked'), 'completeness_checking');
    });

    $('body').on('change', '#pochta_easy_return', function () {
        pochtaChangeSettings($('#pochta_easy_return').prop('checked'), 'easy_return');
    });

    $('body').on('change', '#pochta_no_return', function () {
        pochtaChangeSettings($('#pochta_no_return').prop('checked'), 'no_return');
    });

    $('body').on('change', '#pochta_fragile', function () {
        pochtaChangeSettings($('#pochta_fragile').prop('checked'), 'fragile');
    });

    $('body').on('change', '#pochta_sms_notice', function () {
        pochtaChangeSettings($('#pochta_sms_notice').prop('checked'), 'sms_notice');
    });

    $('body').on('change', '#pochta_electronic_notice', function () {
        pochtaChangeSettings($('#pochta_electronic_notice').prop('checked'), 'electronic_notice');
    });

    $('body').on('change', '#pochta_order_of_notice', function () {
        pochtaChangeSettings($('#pochta_order_of_notice').prop('checked'), 'order_of_notice');
    });

    $('body').on('change', '#pochta_simple_notice', function () {
        pochtaChangeSettings($('#pochta_simple_notice').prop('checked'), 'simple_notice');
    });

    $('body').on('change', '#pochta_wo_mail_rank', function () {
        pochtaChangeSettings($('#pochta_wo_mail_rank').prop('checked'), 'wo_mail_rank');
    });

    $('body').on('change', '#pochta_vsd', function () {
        pochtaChangeSettings($('#pochta_vsd').prop('checked'), 'vsd');
    });

    $('#pochta_mail_category').change(function () {
        pochtaChangeSettings($(this).val(), 'mail_category');
    });

    $('#pochta_mail_type').change(function () {
        pochtaChangeSettings($(this).val(), 'mail_type');
    });

    $('#pochta_dimension_type').change(function () {
        pochtaChangeSettings($(this).val(), 'dimension_type');
    });

    $('.pochta-send').on('click', function () {
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '/phpshop/modules/pochta/ajax/ajax.php',
            type: 'post',
            data: {
                operation: 'send',
                orderId: $('input[name="pochta_order_id"]').val()
            },
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == false) {
                    var errors = '';
                    for (var errorKey in json['errors']) {
                        errors += 'Код ошибки: ' + json['errors'][errorKey]['code'] + '<br>';
                        errors += 'Описание ошибки: ' + json['errors'][errorKey]['description'] + '<br>';
                    }
                    $.MessageBox({
                        buttonDone: false,
                        buttonFail: locale.close,
                        input: false,
                        message: errors
                    })
                } else {
                    $.MessageBox({
                        buttonDone: false,
                        buttonFail: locale.close,
                        input: false,
                        message: 'Заказ успешно отправлен'
                    });
                    $('.pochta-status').html('Отправлен');
                    $('.pochta_actions').css('display', 'none');
                    $("#pochta-table input[type=checkbox]").attr('disabled', true);
                    $("#pochta-table select").attr('disabled', true);
                }
            }
        });
    });
});

function pochtaChangePaymentStatus(value) {
    if (value === true) {
        value = 1;
    }
    if (value === false) {
        value = 0;
    }

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/pochta/ajax/ajax.php',
        type: 'post',
        data: {
            operation: 'changePaymentStatus',
            value: value,
            orderId: $('input[name="pochta_order_id"]').val()
        },
        dataType: "json",
        async: false,
        success: function (json) {}
    });
}

function pochtaChangeSettings(value, key) {
    if (value === true) {
        value = 1;
    }
    if (value === false) {
        value = 0;
    }

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/pochta/ajax/ajax.php',
        type: 'post',
        data: {
            operation: 'changeSettings',
            field: key,
            value: value,
            orderId: $('input[name="pochta_order_id"]').val()
        },
        dataType: "json",
        async: false,
        success: function (json) {}
    });
}

function pochtaCallback(result) {

    var message = locale.pochta.pickup+': ' + result.indexTo + ', ' + result.deliveryDescription.description;

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/pochta/ajax/ajax.php',
        type: 'post',
        data: {
            operation: 'changeAddress',
            cost: result['price'],
            info: message,
            orderId: $('input[name="pochta_order_id"]').val()
        },
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
}
