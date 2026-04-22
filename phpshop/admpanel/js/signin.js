$().ready(function () {

    // Ошибка
    $(".form-control").on('input', function () {
        $(this).parent('.input-group').removeClass('has-error');
    });


    // Восстановление пароля
    $("#remember-me").on('click', function () {
        $('input[name=pas]').removeAttr('required');
        $('input[name=actionID]').detach();
    });

    // Смена цветовой темы
    $('#theme').on('changed.bs.select', function (e) {
        var theme = $(this).val();
        $('#form-signin').fadeOut('slow', function () {
            $('#bootstrap_theme').attr('href', './css/bootstrap-theme-' + theme + '.css');
            $('#form-signin').fadeIn('slow');
        });

    });

    // Отображение пароля
    $(".password-view").on('click', function (event) {
        event.preventDefault();
        if ($('input[name=pas]').attr("type") == 'password') {
            $('input[name=pas]').attr("type", "text");
            $('.glyphicon-eye-close').removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
        } else {
            $('input[name=pas]').attr("type", "password");
            $('.glyphicon-eye-open').addClass('glyphicon-eye-close').removeClass('glyphicon-eye-open');
        }

    });

    if ($('#message').html() != "") {
        $.MessageBox({
            buttonDone: "OK",
            message: $('#message').html()
        })
        $('input[name=pas]').val('');
    }

});