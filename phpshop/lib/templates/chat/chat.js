$().ready(function () {
    $('.chat').toggleClass('chat-display');
    $('#chat_body').css('display', 'none');
    $('#chat_form').css('display', 'none');
    $('.chat_fullscreen_loader').css('display', 'block');
    $('#chat_fullscreen').css('display', 'none');

    // Открытие чата
    $('#prime').click(function () {
        //$('.chat').css('display', 'none');
        $('.chat').toggleClass('chat-display');
        $('.prime').toggleClass('zmdi-comment-outline');
        $('.prime').toggleClass('zmdi-close');
        $('.prime').toggleClass('is-active');
        $('.prime').toggleClass('is-visible');
        $('#prime').toggleClass('is-float');
        $('.chat').toggleClass('is-visible');
        $('.fab-chat').toggleClass('is-visible');
    });

    // Полный экран чата
    $('#chat_fullscreen_loader').click(function (e) {
        $('.fullscreen').toggleClass('zmdi-window-maximize');
        $('.fullscreen').toggleClass('zmdi-window-restore');
        $('.chat').toggleClass('chat_fullscreen');
        $('.fab-chat').toggleClass('is-hide');
        $('.header_img').toggleClass('change_img');
        $('.img_container').toggleClass('change_img');
        $('.chat_header').toggleClass('chat_header2');
        $('.fab_field').toggleClass('fab_field2');
        $('.chat_converse').toggleClass('chat_converse2');
    });
    
    // Согласие
    $("body").on("click", '.message-list input[name=rule]', function () {
       $('#prime').click();
    });

    // Формат ввода телефона
    $("body").on("click", '.message-list input[name=tel]', function () {
        if (PHONE_FORMAT && PHONE_MASK) {

            $(".message-list input[name=tel]").mask(PHONE_MASK, {
                completed: function () {
                    $('.message-list .dialog-reg-tel').removeClass('has-error');
                }
            });

        }
    });

    // Мессенджеры
    $('body').on('click', '.messenger-button', function (event) {
        window.open('https://'+$(this).attr('data-url'));
    });

    // Подсказки
    $('body').on('click', '.dialog-answer', function (event) {

        var data = [];
        data.push({name: 'answer', value: $(this).attr('data-answer')});

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
                    $('.message-list').append(json['message']);
                    $('.message-list').animate({scrollTop: $('.message-list').prop('scrollHeight')}, 2000);
                }
            }
        });

    });

    // Отправление сообщения
    $("body").on('click', ".send-message", function (e) {
        e.preventDefault();
        var data = [];
        
        // Обработка персональных данных
        if($('.message-list input[name="chat-rule"]').last().length &&  $('.message-list input[name="chat-rule"]:checkbox:checked').last().length == 0)
            return false;

        // Регистрация
        if ($('textarea#message').attr('disabled') == 'disabled') {

            var mail = $('.message-list input[name="mail"]').last().val();
            var name = $('.message-list input[name="name"]').last().val();
            var pas = $('.message-list input[name="password"]').last().val();
            var tel = $('.message-list input[name="tel"]').last().val();

            if (tel === undefined)
                var tel = '        ';

            if (mail.length > 5 && name.length > 3 && tel.length > 5) {
                data.push({'name': 'mail', value: mail});
                data.push({'name': 'name', value: name});
                data.push({'name': 'pas', value: pas});
                data.push({'name': 'tel', value: tel});
                data.push({'name': 'reg', value: 1});

                $.ajax({
                    mimeType: 'text/html; charset=' + locale.charset,
                    url: ROOT_PATH + '/phpshop/ajax/dialog.php',
                    type: 'post',
                    dataType: "json",
                    data: data,
                    async: false,
                    success: function (json) {

                        if (json['num'] > 0) {

                            if (json['status'] == 1) {
                                $('textarea#message').removeAttr('disabled');

                                // Разблокировка ссылок
                                var telegram_link = $('.btn-telegram').attr('href');
                                $('.btn-telegram').removeClass('disabled').attr('href', telegram_link + json['bot']);

                                var vk_link = $('.btn-vk').attr('href');
                                $('.btn-vk').removeClass('disabled').attr('href', vk_link + json['bot']);
                            }

                            $('.message-list .dialog-reg-mail, #message-list .dialog-reg-name').removeClass('has-error');
                            $('.message-list').append(json['message']);
                            $('.message-list').animate({scrollTop: 10000});
                            $('textarea#message').focus();
                        }

                    }
                });
            } else {
                if (mail.length < 5)
                    $('.message-list .dialog-reg-mail').addClass('has-error');
                else
                    $('.message-list .dialog-reg-mail').removeClass('has-error');

                if (name.length < 3)
                    $('.message-list .dialog-reg-name').addClass('has-error');
                else
                    $('.message-list .dialog-reg-name').removeClass('has-error');

                if (tel.length < 5)
                    $('.message-list .dialog-reg-tel').addClass('has-error');
            }

        }
        // Сообщение
        else {

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
        }
    });

    // Загрузка диалогов
    $('#prime').on('click', function (event) {

        var data = [];
        data.push({name: 'new', value: 0});
        data.push({name: 'path', value: 'chat'});

        // Очистка
        $('.message-list').html(null);

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: ROOT_PATH + '/phpshop/ajax/dialog.php',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['num'] > 0) {
                    $('.message-list').append(json['message']);
                    $('.message-list').animate({scrollTop: 10000});
                }

                $('#prime').css('animation', 'trambling-animation 0.7s').css('animation-iteration-count', 0);
            }
        });
    });

    $('#message').on('click', function () {
        $('#prime').css('animation', 'trambling-animation 0.7s').css('animation-iteration-count', 0);
    });


    // Проверка новых диалогов
    setInterval(function () {

        var data = [];
        data.push({name: 'new', value: 1});
        data.push({name: 'path', value: 'chat'});

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
                    $('.message-list').append(json['message']);
                    $('.message-list').animate({scrollTop: $('.message-list').prop('scrollHeight')}, 2000);

                    if (json['animation'] == 1)
                        $('#prime').css('animation', 'trambling-animation 0.7s').css('animation-iteration-count', 'infinite');
                    else
                        $('#prime').css('animation', 'trambling-animation 0.7s').css('animation-iteration-count', 0);
                }
            }
        });

    }, 3000);
});