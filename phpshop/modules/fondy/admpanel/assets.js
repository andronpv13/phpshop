$(document).ready(function () {
    $('input[name="mode_work_new"]').change(function () {
        if ($(this).val() == 'test') {
            $('input[name="merchant_id_new"]').val('1396424');
            $('input[name="password_new"]').val('test');
            $('.test-mode').removeClass('hidden');
        } else {
            $('input[name="merchant_id_new"]').val('');
            $('input[name="password_new"]').val('');
            $('.test-mode').addClass('hidden');
        }
    });
    $('input[name="merchant_id_new"], input[name="password_new"]').keyup(function () {
        $('input[value="work"]').prop('checked', true);
        $('.test-mode').addClass('hidden');
    });

    $('select[name="payment_type_new"]').change(function () {
        console.log('sds');
        if ($(this).val() == 'redirect') {
            $('.status-checkout').hide();
        } else {
            $('.status-checkout').show();
        }
    });

    if ($('select[name="payment_type_new"]').val() == 'redirect') {
        $('.status-checkout').hide();
    }
});