
$().ready(function() {

    // Чек возврата
    $("body").on('click', "#cloudpayments", function(event) {
        event.preventDefault();
        
        var operation = $(this).attr('data-operation');
        if(operation == 'sell')
            text = locale.confirm_sell;
        else text = locale.confirm_refund;

        if (confirm(text)) {
            var data = [];
            data.push({name: 'operation', value:  operation});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'id', value: $(this).attr('data-id')});
            $('#cloudpayments').css('display', 'none');
            $('#refund_alert').html('Не забудьте вернуть денежные средства через ваш платёжный сервис/шлюз');
            $('#operation-status').removeClass('text-success').addClass('text-danger').html('Возврат');
            $.ajax({
                mimeType: 'text/html; charset='+locale.charset,
                url: '../modules/cloudkassir/api.php',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function(json) {
                    if (json['status'] != 1) {
                        alert(locale.save_false);
                    }
                }

            });
        }
    });
});