/**
 * Поддержка JQuery функций
 * @package PHPShopJavaScript
 * @author PHPShop Software
 * @version 1.7
 */

// Иконки в основном меню категорий
var MEGA_MENU_ICON = false;

// Меню дублирующих категорий вертикально
var CATALOG_MENU = true;

// Фасетный фильтр
var FILTER = true;

var BRAND_MENU = true;

// Показывать пагинацию при динамической прокрутки товаров
var AJAX_SCROLL_HIDE_PAGINATOR = false;

// Папка размещения от корня
var ROOT_PATH = '';

// Фиксация главного меню
var FIXED_NAVBAR = true;

// DaData.ru Token
var DADATA_TOKEN = false;



// HTML анимации загрузки при аякс запросах
var waitText = '<span class="wait">&nbsp;</span>';

// Комментарии
function commentList(xid, comand, page, cid) {
    var message = "";
    var rateVal = 0;

    if (page === undefined) {
        page = 0;
    }

    if (cid === undefined) {
        cid = 0;
    }


    if (comand == "add") {
        message = $('#message').val();
        if (message == "") {
            return false;
        }
        if ($('input[name=rate][type=radio]:checked').val()) {
            rateVal = $('input[name=rate][type=radio]:checked').val();
        }
    }

    if (comand == "edit_add") {
        message = $('#message').val();
        cid = $('#commentEditId').val();
        $('#commentButtonAdd').show();
        $('#commentButtonEdit').hide();
    }

    if (comand == "dell") {
        if (confirm(locale.commentList.dell)) {
            cid = $('#commentEditId').val();
            $('#commentButtonAdd').show();
            $('commentButtonEdit').hide();
        } else {
            cid = 0;
        }
    }

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/comment.php',
        type: 'post',
        data: 'xid=' + xid + '&comand=' + comand + '&type=json&page=' + page + '&rateVal=' + rateVal + '&message=' + message + '&cid=' + cid,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {

                if (comand == "edit") {
                    $('#message').val(json['comment']);
                    $('#commentButtonAdd').hide();
                    $('#commentButtonEdit').show();
                    $('#commentButtonEdit').show();
                    $('#commentEditId').val(cid);
                } else {
                    document.getElementById('message').value = "";
                    if (json['status'] == "error") {
                        mesHtml = locale.commentList.mesHtml;
                        mesSimple = locale.commentList.mesHtml;

                        showAlertMessage(mesHtml);

                        if ($('#evalForCommentAuth')) {
                            eval($('#evalForCommentAuth').val());
                        }
                    }
                    $('#commentList').html(json['comment']);
                }
                if (comand == "edit_add") {
                    mes = locale.commentList.mes;
                    showAlertMessage(mes);

                }
                if (comand == "add" && json['status'] != "error") {
                    mes = locale.commentList.mes;
                    showAlertMessage(mes);
                }
            }
        }
    });
}


// Локализация
var locale_def = {
    commentList: {
        mesHtml: "Функция добавления комментария возможна только для авторизованных пользователей.\n<a href='/users/?from=true'>Авторизуйтесь или пройдите регистрацию</a>.",
        mesSimple: "Функция добавления комментария возможна только для авторизованных пользователей.\nАвторизуйтесь или пройдите регистрацию.",
        mes: "Ваш комментарий будет доступен другим пользователям только после прохождения модерации...",
        dell: "Вы действительно хотите удалить комментарий?",
    },
    OrderChekJq: {
        badReqEmail: "Пожалуйста, укажите корректный E-mail",
        badReqName: "Обратите внимание,\nимя должно состоять не менее чем из 3 букв",
        badReq: "Обратите внимание,\nесть поля, обязательные для заполнения",
        badDelivery: "Пожалуйста,\nвыберите доставку",
    },
    commentAuthErrMess: "Добавить комментарий может только авторизованный пользователь.\n<a href='" + ROOT_PATH + "/users/?from=true'>Пожалуйста, авторизуйтесь или пройдите регистрацию</a>.",
};

// вывод сообщений после доабвление в корзину, сравнение, вишлист и т.д.
function showAlertMessage(message, danger) {

    if (typeof danger != 'undefined') {
        if (danger === true) {
            danger = 'danger';
        }
        $('.success-notification').find('.alert').addClass('alert-' + danger);
    } else {
        $('.success-notification').find('.alert').removeClass('alert-danger');
        $('.success-notification').find('.alert').removeClass('alert-info');
    }

    var messageBox = '.success-notification';
    var innerBox = '#notification .notification-alert';

    if ($(messageBox).length > 0) {
        $(innerBox).html(' ');
        $(innerBox).html(message);
        $(messageBox).fadeIn('slow');

        setTimeout(function () {
            $(messageBox).delay(500).fadeOut(1000);
        }, 5000);
    }
}

// проверка валидности email
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

// добавление товара в вишлист
function addToWishList(product_id, parent_id = 0) {

    if (parent_id === undefined) {
        parent_id = '';
    }

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/wishlist.php',
        type: 'post',
        data: 'product_id=' + product_id + '&parent_id=' + parent_id,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $(".wishlistcount").html(json['count']);
            }
        }
    });
}

// просчёт доставки
function UpdateDeliveryJq(xid, param, stop_hook) {

    var sum = $("#OrderSumma").val();
    var wsum = Number($("#WeightSumma").text());
    var vwsum = Number($("#VolumeWeightSumma").text());

    if (param === undefined) {
        param = '';
    }

    $("form[name='forma_order'] input[name=dostavka_metod]").attr('disabled', true);
    $(this).html(waitText);

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/delivery.php',
        type: 'post',
        data: 'type=json&xid=' + xid + '&sum=' + sum + '&wsum=' + wsum + '&vwsum=' + vwsum + param,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                $("#DosSumma").html(json['delivery']);
                $("#d").val(xid);
                $("#d").data('free', json['free_delivery']);
                $("#TotalSumma").html(json['total']);
                $("#seldelivery").html(json['dellist']);
                if ($('input[name="dostavka_metod"]:disabled').length > 0) {
                    $('input[name="dostavka_metod"]:disabled').each(function (index, element) {
                        if ($(element).attr('title')) {
                            $(element).closest('label').tooltip({
                                title: $(element).attr('title'),
                                placement: 'top'
                            });
                        }
                    });
                }

                $("#userAdresData").hide();
                $("#seldelivery").html(json['userAdresData']);
                $("#userAdresData").html(json['adresList']);
                $("#userAdresData").fadeIn("slow");

                $('#deliveryInfo').html(null);

                // блокировка способов оплат
                var paymentStop = $('input[name="dostavka_metod"]:checked').attr('data-option');
                if (paymentStop !== undefined) {
                    var payment_array = paymentStop.split(",");
                }

                $('.paymOneEl input[name="order_metod"]').each(function () {
                    $(this).attr('disabled', false);
                });

                if ($.isArray(payment_array)) {
                    $.each(payment_array, function (index, value) {
                        $('.paymOneEl input[data-option="payment' + value + '"]').attr('disabled', true);
                        $('.paymOneEl input[data-option="payment' + value + '"]').attr('checked', false);
                    });
                }

                if ($("input#order_metod:checked").length == 0) {
                    $('input#order_metod').each(function () {
                        if (!this.disabled) {
                            this.checked = true;
                            return false;
                        }
                    });
                }

                // учет хука доставки
                if (json['hook'] !== undefined && json['hook'] !== "" && stop_hook === undefined) {
                    eval(json['hook']);
                }

                // заполняем фио значением из личных данных
                if ($("form[name='forma_order'] input[name='fio_new']").val() == "") {
                    $("form[name='forma_order'] input[name='fio_new']").val($("form[name='forma_order'] input[name='name_new']").val());
                }

                // заполняем данными адрес, если выбран
                $("#adres_id").change();

                // Подсказки DaData.ru
                if (typeof $('#body').attr('data-token') !== 'undefined' && $('#body').attr('data-token').length) {
                    var DADATA_TOKEN = $('#body').attr('data-token');
                }
                if (DADATA_TOKEN !== false && DADATA_TOKEN !== undefined) {
                    var token = DADATA_TOKEN;
                    var type = "ADDRESS";
                    var $city = $("form[name='forma_order'] input[name='city_new']");
                    var $street = $("form[name='forma_order'] input[name='street_new']");
                    var $house = $("form[name='forma_order'] input[name='house_new']");

                    $city.suggestions({
                        token: token,
                        type: type,
                        hint: false,
                        bounds: "city-settlement",
                        onSelect: showPostalCode,
                        onSelectNothing: clearPostalCode
                    });

                    $street.suggestions({
                        token: token,
                        type: type,
                        hint: false,
                        bounds: "street",
                        constraints: $city,
                        onSelect: showPostalCode,
                        onSelectNothing: clearPostalCode
                    });

                    $house.suggestions({
                        token: token,
                        type: type,
                        hint: false,
                        bounds: "house",
                        constraints: $street,
                        onSelect: showPostalCode,
                        onSelectNothing: clearPostalCode
                    });
                    function showPostalCode(suggestion) {

                        // Хук на выбор индекса
                        if (typeof showPostalCodeHook !== 'undefined' && typeof showPostalCodeHook === 'function') {
                            showPostalCodeHook(suggestion.data.postal_code);
                        } else {
                            $("[name='index_new']").val(suggestion.data.postal_code);
                        }
                    }
                    function clearPostalCode() {

                        // Хук на выбор индекса
                        if (typeof clearPostalCodeHook !== 'undefined' && typeof clearPostalCodeHook === 'function') {
                            showPostalCodeHook(suggestion.data.postal_code);
                        } else {
                            $("[name='index_new']").val("");
                        }

                    }
                    $("form[name='forma_order'] input[name='org_name_new']").suggestions({
                        token: DADATA_TOKEN,
                        type: "PARTY",
                        count: 5
                    });
                }
            }
        }
    });
}

// Проверка формы заказа
function OrderChekJq() {

    if ($("#makeyourchoise").val() != "DONE") {
        bad = 1;
    } else {
        bad = 0;
    }

    var badReq = 0;
    var badReqName = 0;
    var badReqEmail = 0;
    var badReqTel = 0;
    $('form[name="forma_order"] .req').each(function () {

        // проверяем валидность e-mail и имя пользователя
        if ($(this).attr('name') == 'mail' && !IsEmail($(this).val())) {
            badReqEmail = 1;
        }

        if ($(this).attr('name') == 'name_new') {

            if ($(this).val().length < 3) {
                badReqName = 1;
            }
        }

        if ($(this).val() == "" || ($(this).attr('name') == 'rule' && $(this).prop('checked') == false) || (badReqEmail && $(this).attr('name') == 'mail') || (badReqName && $(this).attr('name') == 'name_new')) {

            // переходим по якорю на самое верхнее незаполненое поле
            if (badReq == 0) {
                var destination = $(this).parent().offset().top;
                var par = $(this);
                $("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 800, function () {
                    par.focus();
                });
            }

            if (badReq == 0) {
                badReq = 1;
            }

            $(this).addClass('reqActiv');
        }

    });

    if (badReqEmail == 1) {
        showAlertMessage(locale_def.OrderChekJq.badReqEmail);
    } else if (badReqName == 1) {
        showAlertMessage(locale_def.OrderChekJq.badReqName);
    } else if (badReq == 1) {
        showAlertMessage(locale_def.OrderChekJq.badReq);
    } else if (bad == 1) {
        showAlertMessage(locale_def.OrderChekJq.badDelivery);
        var destination = $('#seldelivery').offset().top;
        $("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 800);
    } else {
        $('form[name="forma_order"]').submit();
    }
}

// функция генерации пароля
function wpiGenerateRandomNumber(limit) {
    limit = limit || 8;
    var password = '';
    var chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    var list = chars.split('');
    var len = list.length, i = 0;
    do {
        i++;
        var index = Math.floor(Math.random() * len);
        password += list[index];

    } while (i < limit);
    return password;
}

$(document).ready(function () {

    // DaData.ru токен
    if (typeof $('#body').attr('data-token') !== 'undefined' && $('#body').attr('data-token').length) {
        var DADATA_TOKEN = $('#body').attr('data-token');
    }

    // закрытие сообщения по клику на иконку крестика
    $('#notification').on('click', 'img', function () {
        $(this).parent().fadeOut('slow', function () {
            $(this).hide();
        });
    });

    // логика генерации пароля при регистрации
    $(".passGen").click(function () {
        var str = wpiGenerateRandomNumber(8);
        $(this).closest('form').find("input[name='password_new'], input[name='password_new2']").val(str);
        showAlertMessage('Ваш сгенерированный пароль будет выслан на ваш email после регистрации');
    });

    // сбрасываем оплаты и юр данные при сбросе все формы
    $('form').on('reset', function (e) {
        setTimeout(function () {
            $("#order_metod").change();
        });
    });

    // Варианты оплат выводятся радиобоксами
    $("input#order_metod").change(function () {
        var str = "";
        str = ".showYurDataForPaymentClass" + $("input#order_metod:checked").val();
        if (str != "" && $(str).html()) {
            $("#showYurDataForPaymentLoad").html($(str).clone().removeClass().show());
            if (DADATA_TOKEN !== false && DADATA_TOKEN !== undefined) {
                $("#showYurDataForPaymentLoad input[name='org_name_new']").suggestions({
                    token: DADATA_TOKEN,
                    type: "PARTY",
                    count: 5,
                    onSelect: showSuggestion
                });
                $("#showYurDataForPaymentLoad input[name='org_bank_new']").suggestions({
                    token: DADATA_TOKEN,
                    type: "BANK",
                    count: 5,
                    onSelect: showSuggestionBank
                });
            }
        } else {
            $("#showYurDataForPaymentLoad").html('');
        }
    });

    // выделяем первую в списке оплату
    $("input#order_metod:first").attr('checked', 'checked').change().closest('.paymOneEl').addClass('active');

    // при изменении адреса, заполняем соотв. поля
    $("#adres_id").change(function () {
        var str = "";
        $(this).find("option:selected").each(function () {
            str = $(this).val();
        });
        if (!str) {
            return;
        }

        // получаем данные адресов
        var obj = jQuery.parseJSON($("input:hidden.adresListJson").val());
        $.each(obj, function (index, value) {
            $.each(value, function (index2, value2) {
                $("input[name='" + index2 + "']").val("");
            });
        });

        $.each(obj[str], function (index, value) {

            if (value != "") {
                name = "input[name='" + index + "']";
                $(name).val(value);
                $(name).removeClass('reqActiv');

                // КПП для ИП
                if (index == 'org_inn_new' && value.length == 12) {
                    $("input[name='org_kpp_new']").removeClass('req').attr('disabled', true);
                    ;
                }
            }


        });
    }).change();

    // подбор городов из списка
    $("form[name='forma_order']").on('change', 'select.citylist', function () {
        var par = $(this).attr("name");
        if (par == "city_new") {
            return false;
        }
        if (par == "country_new") {
            $("form[name='forma_order'] select.citylist[name=city_new] option[value!='']").remove();
            $("form[name='forma_order'] select.citylist[name=state_new] option[value!='']").remove();
        }
        if (par == "state_new") {
            $("form[name='forma_order'] select.citylist[name=city_new] option[value!='']").remove();
        }

        $("form[name='forma_order'] select.citylist").attr("disabled", true);
        $(this).after(waitText);
        $.ajax({
            url: ROOT_PATH + '/phpshop/ajax/citylist.php',
            type: 'post',
            data: {
                country: $("form[name='forma_order'] select.citylist[name=country_new] option:selected").attr('for'),
                region: $("form[name='forma_order'] select.citylist[name=state_new] option:selected").attr('for'),
                par: par
            },

            success: function (data) {
                $("#citylist .wait").remove();
                $("form[name='forma_order'] select.citylist[name=country_new]").attr("disabled", false);
                switch (par) {
                    case "country_new":
                        $("form[name='forma_order'] select.citylist[name=state_new]").html(data);
                        $("form[name='forma_order'] select.citylist[name=state_new]").attr("disabled", false);
                        break;
                    case "state_new":
                        $("form[name='forma_order'] select.citylist[name=city_new]").html(data);
                        $("form[name='forma_order'] select.citylist[name=city_new]").attr("disabled", false);
                        $("form[name='forma_order'] select.citylist[name=state_new]").attr("disabled", false);
                        break;
                }
            }
        });
    });

    // выбор способа доставки
    $("form[name='forma_order']").on('click', 'input[name=dostavka_metod]', function () {
        $(this).next().after(waitText);
        UpdateDeliveryJq($(this).val());
    });

    // при вводе Имени пользователя, автоматом прописываем его в адрес если он пуст
    $("form[name='forma_order']").on('change', 'input[name=name_new]', function () {
        if ($("form[name='forma_order'] input[name='fio_new']").val() == "") {
            $("form[name='forma_order'] input[name='fio_new']").val($(this).val());
        }
    });

    // отключаем класс особого выделения для обязательных полей при переходе на них
    $('form[name="forma_order"]').on('keyup blur change', '.req', function () {
        if ($(this).val() != '') {
            $(this).removeClass('reqActiv');
        } else {
            $(this).addClass('reqActiv');
        }
    });

    // Отзывы к товарам
    $('textarea.commentTextarea').on('focus', function () {
        if ($('input#commentAuthFlag').val() == 0) {
            $(this).val("").attr('readonly', 'readonly');
            showAlertMessage(locale_def.commentAuthErrMess);
            if (document.getElementById('evalForCommentAuth')) {
                eval(document.getElementById('evalForCommentAuth').value);
            }
        }
    });

    // Склонение товара в корзине
    var cart_lang = [];
    for (var i = 0; i < 100; i++) {
        cart_lang[i] = 'ов';
    }
    cart_lang[1] = '';
    cart_lang[2] = 'а';
    cart_lang[3] = 'а';
    cart_lang[4] = 'а';
    cart_lang[21] = '';
    cart_lang[22] = 'а';
    cart_lang[23] = 'а';
    cart_lang[24] = 'а';
    if (cart_lang[$('#num').text()] != 'undefined') {
        $('#lang-cart').text('товар' + cart_lang[$('#num').text()]);
    }

    $(".button").click(function () {
        setTimeout(function () {
            if (cart_lang[$('#num').text()] != 'undefined') {
                $('#lang-cart').text('товар' + cart_lang[$('#num').text()]);
            }
        }, 1000);
    });

    // Закрытие сообщения о корзине
    $('#notification').on('close.bs.alert', function (e) {
        e.preventDefault();
        $('#notification').css('display', 'none');
    });

});

// Вывод подсказок DaData.ru в форме юридических данных
function showSuggestion(suggestion) {
    var data = suggestion.data;
    if (!data) {
        return;
    }
    $("input[name='org_inn_new']").val(data.inn);

    // КПП для ИП
    if (data.inn.length == 12) 
        $("input[name='org_kpp_new']").removeClass('req').attr('disabled', true);
    else $("input[name='org_kpp_new']").addClass('req').attr('disabled', false);

    $("input[name='org_kpp_new']").val(data.kpp);
    $("input[name='org_yur_adres_new']").val(data.address.value);
    $("input[name='org_fakt_adres_new']").val(data.address.value);
}

function showSuggestionBank(suggestion) {
    var data = suggestion.data;
    if (!data) {
        return;
    }
    $("input[name='org_bik_new']").val(data.bic);
    $("input[name='org_city_new']").val(data.address.data.city);
    $("input[name='org_kor_new']").val(data.correspondent_account);
}