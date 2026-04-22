
$().ready(function () {

    // Чек возврата
    $("body").on('click', "#atol", function (event) {
        event.preventDefault();

        var operation = $(this).attr('data-operation');
        if (operation == 'sell')
            text = locale.confirm_sell;
        else
            text = locale.confirm_refund;

        if (confirm(text)) {
            var data = [];
            data.push({name: 'operation', value: operation});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'id', value: $(this).attr('data-id')});
            data.push({name: 'manual', value: 1});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '../modules/atol/api.php',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['status'] == 1) {
                        if (Number($.getUrlVar('tab')) !== 4) {
                            window.location.href += '&tab=4';
                        } else {
                            location.reload();
                        }

                    } else {
                        alert(locale.save_false);
                    }
                }

            });
        }
    });
});