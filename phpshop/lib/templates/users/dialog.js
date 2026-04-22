$().ready(function () {

    // Отправление сообщения
    $(".send-message").on('click', function (event) {

        var data = [];
        data.push({'name': 'message', value: $('textarea#message').val()});
        data.push({'name': 'ajax', value: true});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: ROOT_PATH + '/users/message.html',
            type: 'post',
            dataType: "html",
            data: data,
            async: false,
            success: function () {

                // Очистка
                $('textarea#message').val(null);
                $('textarea#message').focus();
            }
        });
    });

    // Проверка новых диалогов
    setInterval(function () {

        var data = [];
        data.push({name: 'new', value: 1});
        data.push({name: 'path', value: 'dialog'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: ROOT_PATH + '/phpshop/ajax/dialog.php',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['num'] > 0) {
                    $('#play-chat').trigger("play");
                    $('#message-list').append(json['message']);
                    $('#message-list').animate({scrollTop: $('#message-list').prop('scrollHeight')}, 2000);
                }
            }
        });

    }, 3000);
});