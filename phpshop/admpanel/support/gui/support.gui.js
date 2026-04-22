
$().ready(function () {
    
    locale.icon_load = null;

    $("textarea[name=message],input[name=file]").on('click', function (event) {
        $(".send-message").removeClass('disabled');
        $(window).unbind("beforeunload");
    });

    $("#attachment-disp").on('click', function (event) {
        $(this).toggleClass('hide');
        $("#attachment").toggleClass('hide');
    });

    $("button[name=noSupport]").on('click', function () {
        window.open('https://help.phpshop.ru/new/');
    });


    // Закрыть заявку
    $(".support-close").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_support_close
        }).done(function () {

            var data = [];
            var id = $.getUrlVar('id');
            data.push({name: 'selectID', value: 2});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionClose'});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=support&id=' + id,
                type: 'post',
                data: data,
                dataType: "html",
                async: false,
                success: function () {
                    window.location.href = '?path=support';
                }

            });
        })
    });

});