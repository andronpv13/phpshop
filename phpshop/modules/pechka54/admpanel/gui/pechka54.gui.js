
$().ready(function() {

    // URL обмена
    $("body").on('change', 'input[name="password_new"]', function() {
        $('[name="kkm_key"]').text(this.value);
    });

    // Чек возврата
    $("body").on('click', "#atol", function(event) {
        event.preventDefault();

        var operation = $(this).attr('data-operation');
        if (operation == 'sell') {
            ofd_type = 'registration';
            text = locale.confirm_sell;
        }
        else if (operation == 'return') {
            text = locale.confirm_refund;
            ofd_type = 'return';
        }
        else {
            ofd_type = 'registration';
            text = locale.confirm_sell;
        }

        if (confirm(text)) {
            $("input[name='ofd_status_new']").val(0);
            $("input[name='ofd_type_new']").val(ofd_type);
            $("input[name='statusi_new']").val(101);
        }
    });
});