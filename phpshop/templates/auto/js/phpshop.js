// добавление товара в корзину
function addToCartList(product_id, num, parent, addname) {

    if (num === undefined)
        num = 1;

    if (addname === undefined)
        addname = '';

    if (parent === undefined)
        parent = 0;

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/cartload.php',
        type: 'post',
        data: 'xid=' + product_id + '&num=' + num + '&xxid=0&type=json&addname=' + addname + '&xxid=' + parent,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $("#num, .cartnum, #mobilnum").html(json['num']).removeClass('d-none');
                $("#sum").html(json['sum']);
                $("#order").removeClass('d-none');
            }
        }
    });
}

// добавление товара в корзину
function addToCompareList(product_id) {

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/compare.php',
        type: 'post',
        data: 'xid=' + product_id + '&type=json',
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $(".numcompare").html(json['num']).removeClass('d-none');
            }
        }
    });
}

// Фотогалерея
function fotoload(xid, fid) {

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/fotoload.php',
        type: 'post',
        data: 'xid=' + xid + '&fid=' + fid + '&type=json',
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                $("#fotoload").fadeOut('slow', function () {
                    $("#fotoload").html(json['foto']);
                    $("#fotoload").fadeIn('slow');
                });
            }
        }
    });
}

function ChangeSkin() {
    document.SkinForm.submit();
}

// Смена валюты
function ChangeValuta() {
    document.ValutaForm.submit();
}

// Создание ссылки для сортировки
function ReturnSortUrl(v) {
    var s, url = "";
    if (v > 0) {
        s = document.getElementById(v).value;
        if (s != "")
            url = "v[" + v + "]=" + s + "&";
    }
    return url;
}

// Проверка наличия файла картинки, вставляем заглушку
function NoFoto(obj, pathTemplate) {
    obj.src = pathTemplate + '/images/shop/no_photo.gif';
}

// Сортировка по всем фильтрам
function GetSortAll() {
    var url = ROOT_PATH + "/shop/CID_" + arguments[0] + ".html?";

    var i = 1;
    var c = arguments.length;

    for (i = 1; i < c; i++)
        if (document.getElementById(arguments[i]))
            url = url + ReturnSortUrl(arguments[i]);

    location.replace(url.substring(0, (url.length - 1)) + "#sort");

}

// Инициализируем таблицу перевода на русский
var trans = [];
for (var i = 0x410; i <= 0x44F; i++)
    trans[i] = i - 0x350; // А-Яа-я
trans[0x401] = 0xA8;    // Ё
trans[0x451] = 0xB8;    // ё

// Таблица перевода на украинский
/*
 trans[0x457] = 0xBF;    // ї
 trans[0x407] = 0xAF;    // Ї
 trans[0x456] = 0xB3;    // і
 trans[0x406] = 0xB2;    // І
 trans[0x404] = 0xBA;    // є
 trans[0x454] = 0xAA;    // Є
 */

// Сохраняем стандартную функцию escape()
var escapeOrig = window.escape;

// Переопределяем функцию escape()
window.escape = function (str)
{

    if (locale.charset == 'utf-8')
        return str;

    else {
        var str = String(str);
        var ret = [];
        // Составляем массив кодов символов, попутно переводим кириллицу
        for (var i = 0; i < str.length; i++)
        {
            var n = str.charCodeAt(i);
            if (typeof trans[n] != 'undefined')
                n = trans[n];
            if (n <= 0xFF)
                ret.push(n);
        }
        return escapeOrig(String.fromCharCode.apply(null, ret));
    }
};

// Ajax фильтр обновление данных
function filter_load(filter_str, obj) {

    $.ajax({
        type: "POST",
        url: '?' + filter_str.split('#').join(''),
        data: {
            ajax: true,
            json: true
        },
        success: function (data)
        {
            if (data === 'empty_sort') {
                showAlertMessage('Товары не найдены', true);
            } else {
                $(".template-product-list").html(data['products']);
                $('#price-filter-val-max').removeClass('has-error');
                $('#price-filter-val-min').removeClass('has-error');
                $("#pagination-block").html(data['pagination']);

                // Блокировкам пустых значений и пересчет количества фильтра
                if (typeof FILTER_CACHE !== "undefined" && FILTER_CACHE) {
                    $('#faset-filter-body [type="checkbox"]').each(function () {

                        if (data['logic'] == 0) {
                            $(this).attr('disabled', 'disabled');
                            $(this).next('.filter-item').addClass('filter-item-hide');
                        } else if (data['logic'] == 1 && $(this).attr('data-count') != 1) {
                            $(this).attr('disabled', 'disabled');
                            $(this).next('.filter-item').addClass('filter-item-hide');
                        }

                        if (FILTER_COUNT && data['logic'] == 0) {
                            $('[data-num="' + $(this).attr('name') + '"]').text(0);
                        }

                        for (var key in data['filter']) {
                            if ($(this).attr('name') == key) {

                                $(this).removeAttr('disabled');
                                $(this).next('.filter-item').removeClass('filter-item-hide');

                                if (FILTER_COUNT && data['logic'] == 0) {
                                    $('[data-num="' + $(this).attr('name') + '"]').text(data['filter'][key]);
                                } else if (data['logic'] == 1 && $(this).attr('data-count') != 1) {
                                    $('[data-num="' + $(this).attr('name') + '"]').text(data['filter'][key]);
                                }
                            }
                        }
                    });
                }
            }
        },
        error: function (data) {
            $(obj).attr('checked', false);

            if ($(obj).attr('name') == 'max') {
                $('#price-filter-val-max').addClass('has-error');
            }
            if ($(obj).attr('name') == 'min') {
                $('#price-filter-val-min').addClass('has-error');
            }

            window.location.hash = window.location.hash.split($(obj).attr('data-url') + '&').join('');
        }

    });
}

// Ценовой слайдер
function price_slider_load(min, max, obj) {

    var hash = window.location.hash.split('min=' + $.cookie('slider-range-min') + '&').join('');
    hash = hash.split('max=' + $.cookie('slider-range-max') + '&').join('');
    hash += 'min=' + min + '&max=' + max + '&';
    window.location.hash = hash;

    filter_load(hash, obj);

    $.cookie('slider-range-min', min);
    $.cookie('slider-range-max', max);

    $(".pagination").hide();

}

// Ajax фильтр событие клика
function faset_filter_click(obj) {

    if (AJAX_SCROLL) {

        var hash;
        var hashes = window.location.href.split('#')[0].slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            if (hash[0].match(/v\[(.*)\]/) && window.location.hash.indexOf(hash[0] + '=' + hash[1]) === -1) {
                window.location.hash += hash[0] + '=' + hash[1] + '&';
            }
        }

        if ($(obj).prop('checked')) {
            window.location.hash += $(obj).attr('data-url') + '&';

        } else {
            hashes = window.location.hash.split($(obj).attr('data-url') + '&').join('');
            if (hashes == '#') {
                history.pushState('', document.title, window.location.pathname);
            } else {
                window.location.hash = hashes;
            }
        }

        filter_load(window.location.hash.split(']').join('][]'), obj);
    } else {

        var href = window.location.href.split('?')[1];

        if (href == undefined)
            href = '';

        var last = href.substring((href.length - 1), href.length);
        if (last != '&' && last != '')
            href += '&';

        if ($(obj).prop('checked')) {
            href += $(obj).attr('data-url').split(']').join('][]') + '&';
        } else {
            href = href.split($(obj).attr('data-url').split(']').join('][]') + '&').join('');
        }

        window.location.href = catalogFirstPage + '?' + href;
    }
}

$().ready(function () {

    // Активация левого меню каталога на странице продукта
    $('.breadcrumb > li > a').each(function () {
        var linkHref = $(this).attr('href');
        $('.sidebarNav').each(function () {
            if ($(this).attr('data-cid') == linkHref) {
                $(this).addClass("show");

                // Иконка вверх
                $('[aria-controls="' + $(this).attr('id') + '"]').addClass('sidebarNavOpen');

                $('.dropdown-item').each(function () {
                    if ($(this).attr('data-id') == $('#body').attr('data-id') && $('#body').attr('data-subpath') == 'CID') {
                        $(this).addClass('text-primary');
                    }
                });
            }
        });

        $('.dropdown-nav-link').each(function () {
            if ($(this).attr('data-id') == $('#body').attr('data-id') && $('#body').attr('data-subpath') == 'CID') {
                $(this).addClass('text-primary');
                $($(this).attr('data-target')).addClass("show");
            }

            if ($(this).attr('href') == linkHref) {
                $(this).addClass('text-primary');
            }
        });

        $('.dropdown-item').each(function () {

            if ($(this).attr('href') == linkHref) {
                $(this).addClass('text-primary');
            }
        });


    });

    $(".filter-btn").on('click', function () {
        $("#faset-filter").fadeIn();
    });
    $(".filter-close").click(function () {
        $("#faset-filter").fadeOut();
    });

    $(".faset-filter-name .close").on('click', function () {
        $("#faset-filter").fadeOut();
    });


    // логика кнопки оформления заказа 
    $("button.orderCheckButton").on("click", function (e) {
        e.preventDefault();
        OrderChekJq();
    });

    $('#price-filter-body input').on('change', function () {
        if (AJAX_SCROLL) {
            price_slider_load($('#price-filter-body input[name=min]').val(), $('#price-filter-body input[name=max]').val(), $(this));
        } else {
            $('#price-filter-form').submit();
        }
    });

    // Ценовой слайдер
    $("#slider-range").on("slidestop", function (event, ui) {

        if (AJAX_SCROLL) {

            // Сброс текущей страницы
            count = current;

            price_slider_load(ui.values[ 0 ], ui.values[ 1 ]);
        } else {
            $('#price-filter-form').submit();
        }
    });

    // Фасетный фильтр
    if (FILTER && $("#sorttable table td").html()) {
        $("#faset-filter-body").html($("#sorttable table td").html());
        $("#faset-filter").removeClass('d-none');
        $('.filter-btn').addClass('visible-filter');
    } else {
        $("#faset-filter").hide();

    }

    if (!FILTER) {
        $("#faset-filter").hide();
        $("#sorttable").removeClass('d-none');
    }

    // Направление сортировки в брендах
    $('#filter-well-select-stat').on('change', function () {
        window.location.href = this.value;
    });

    // Направление сортировки
    $('#filter-well-select').on('change', function () {

        if (AJAX_SCROLL) {

            count = current;
            var hashes = window.location.hash;
            hashes = hashes.split('s=1&').join('');
            hashes = hashes.split('s=2&').join('');
            hashes = hashes.split('s=3&').join('');
            hashes = hashes.split('f=2&').join('');
            hashes = hashes.split('f=1&').join('');
            hashes += 's=' + this.value + '&';
            window.location.hash = hashes;

            filter_load(window.location.hash);
        } else {

            var href = window.location.href.split('?')[1];

            if (href == undefined)
                href = '';

            var last = href.substring((href.length - 1), href.length);
            if (last != '&' && last != '')
                href += '&';

            href = href.split('s=1&').join('');
            href = href.split('s=2&').join('');
            href = href.split('f=2&').join('');
            href = href.split('f=1&').join('');
            href += $(this).attr('name') + '=' + this.value;
            window.location.href = '?' + href;
        }

    });


    // Направление сортировки
    $('#filter-well .filter-item').on('click', function (event) {
        event.preventDefault();
        $('#filter-well .nav-link').removeClass('active');
        $(this).addClass('active');


        if (AJAX_SCROLL) {

            count = current;

            var hashes = window.location.hash;
            hashes = hashes.split($(this).attr('name') + '=1&').join('');
            hashes = hashes.split($(this).attr('name') + '=2&').join('');
            hashes += $(this).attr('name') + '=' + $(this).attr('value') + '&';
            window.location.hash = hashes;

            filter_load(window.location.hash);
        } else {

            var href = window.location.href.split('?')[1];

            if (href == undefined)
                href = '';

            var last = href.substring((href.length - 1), href.length);
            if (last != '&' && last != '')
                href += '&';

            href = href.split($(this).attr('name') + '=1&').join('');
            href = href.split($(this).attr('name') + '=2&').join('');
            href += $(this).attr('name') + '=' + $(this).attr('value');
            window.location.href = '?' + href;
        }
    });

    // Загрузка результата отбора при переходе
    if (window.location.hash != "" && $("#sorttable table td").html()) {

        var filter_str = window.location.hash.split(']').join('][]');

        // Проставление чекбоксов
        $.ajax({
            type: "POST",
            url: '?' + filter_str.split('#').join(''),
            data: {
                ajaxfilter: true
            },
            success: function (data)
            {
                if (data) {
                    $("#faset-filter-body").html(data);
                    $("#faset-filter-body").html($("#faset-filter-body").find('td').html());

                    // Загрузка результата отборки
                    filter_load(filter_str);
                }
            }
        });
    }

    // Ajax фильтр
    $('#faset-filter-body').on('change', 'input:checkbox', function () {

        // Сброс текущей страницы
        count = current;
        faset_filter_click($(this));
    });


    // Сброс фильтра
    $('#faset-filter-reset').on('click', function (event) {
        if (AJAX_SCROLL) {
            event.preventDefault();
            $("#faset-filter-body").html($("#sorttable table td").html());
            filter_load('');
            history.pushState('', document.title, window.location.pathname);
            $.removeCookie('slider-range-min');
            $.removeCookie('slider-range-max');
            $(".pagination").show();

            // Сброс текущей страницы
            count = current;

            // Сброс слайдера
            var max = $("#slider-range").slider("option", "max");
            var min = $("#slider-range").slider("option", "min");
            $('#price-filter-body input[name=min]').val(min);
            $('#price-filter-body input[name=max]').val(max);
            $("#slider-range").slider({
                values: [min, max]
            });
        }

    });

    // Пагинация товаров
    $('.pagination a').on('click', function (event) {
        if (AJAX_SCROLL) {
            event.preventDefault();
            window.location.href = $(this).attr('href') + window.location.hash;
        }
    });

    // быстрый переход
    $(document).on('keydown', function (e) {
        if (e == null) {
            key = event.keyCode;
            var ctrl = event.ctrlKey;
        } else {
            key = e.which;
            var ctrl = e.ctrlKey;
        }
        if ((key == '123') && ctrl)
            window.location.replace(ROOT_PATH + '/phpshop/admpanel/');
        if (key == '120') {
            $.ajax({
                url: ROOT_PATH + '/phpshop/ajax/info.php',
                type: 'post',
                data: 'type=json',
                dataType: 'json',
                success: function (json) {
                    if (json['success']) {
                        confirm(json['info']);
                    }
                }
            });
        }
    });

    // выбор каталога поиска
    $(".cat-menu-search").on('click', function () {
        $('#cat').val($(this).attr('data-target'));
        $('#catSearchSelect').html($(this).html());
    });


    $('.comment-more').bind('click', function (event) {
        event.preventDefault();
        $('#commentList .media:nth-child(n+3)').fadeToggle();
        $(this).toggleClass('click');
        $(this).toggleClass('hide-click');
        $('.comment-more.click').text('Скрыть');
        $('.comment-more.hide-click').text('Показать еще');
    });

    // убираем пустые закладки подробного описания
    if ($('#files').html() != 'Нет файлов')
        $('#filesTab').addClass('show');

    if ($('#vendorenabled').html() != '')
        $('#settingsTab').addClass('show');

    if ($('#pages').html() != '')
        $('#pagesTab').addClass('show');

    // убираем меню брендов
    if (BRAND_MENU === false) {
        $('#brand-menu').hide();
    }

    // добавление в корзину
    $('body').on('click', '.addToCartList', function (e) {
        e.preventDefault();
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'));
        $(this).attr('disabled', 'disabled');
        $(this).addClass('btn-soft-primary');
        $(this).text(locale.incart);
        $('#order').removeClass('d-none');
    });

    // изменение количества товара для добавления в корзину
    $('body').on('change', '.addToCartListNum', function () {
        var num = (Number($(this).val()) || 1);
        var id = $(this).attr('data-uid');
        if (num > 0) {
            $(".addToCartList").each(function () {
                if ($(this).attr('data-uid') === id)
                    $('.addToCartList[data-uid=' + id + ']').attr('data-num', num);
            });
        }

    });

    // добавление в корзину подтипа
    $(".addToCartListParent").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-parent'));
        $('[itemprop="price"]').html($(this).attr('data-price'));
    });

    // добавление в корзину опции
    $(".addToCartListOption").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-uid'), $('#allOptionsSet' + $(this).attr('data-uid')).val());
    });

    // добавление в wishlist
    $('body').on('click', '.addToWishList', function (event) {
        event.preventDefault();
        addToWishList($(this).attr('data-uid'));
        $(".wishlistcount").removeClass('d-none');
        $(this).addClass('active');
    });

    // добавление в compare
    $('body').on('click', '.addToCompareList', function (event) {
        event.preventDefault();
        addToCompareList($(this).attr('data-uid'));
    });

    // отправка сообщения администратору из личного кабинета
    $("#CheckMessage").on('click', function () {
        if ($("#message").val() != '')
            $("#forma_message").submit();
    });

    // Подсказки 
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

    // Переход из прайса на форму с описанием
    $('#price-form').on('click', function (event) {
        event.preventDefault();
        if ($(this).attr('data-uid') != "" && $(this).attr('data-uid') != "ALL") {
            window.location.replace("../shop/CID_" + $(this).attr('data-uid') + ".html");
        }
    });

    // Ajax поиск
    $(".search-slide-down-suggestions").hide();
    $("#searchSlideDownControl").on('input', function () {
        var words = $(this).val();
        if (words.length > 2) {
            $.ajax({
                type: "POST",
                url: ROOT_PATH + "/search/",
                data: {
                    words: escape(words),
                    set: 2,
                    ajax: true
                },
                success: function (data)
                {

                    // Результат поиска
                    if (data != 'false') {

                        if (data != $("#searchSlideDownContent").html()) {
                            $("#searchSlideDownContent").html(data);
                            $(".search-slide-down-suggestions").show();
                        }
                    } else {
                        $("#searchSlideDownContent").html(null);
                        $(".search-slide-down-suggestions").hide();
                    }
                }
            });
        } else {
            $("#searchSlideDownContent").html(null);
            $(".search-slide-down-suggestions").hide();
        }
    });

    // оформление кнопок
    $(".btn-default").addClass('btn-primary').addClass('transition-3d-hover');

    // Показ блока сейчас кто-то купил
    $(".modal-nowBuy").fadeIn(2000).delay(7000).fadeOut(1000);

    // Закрыть блок сейчас кто-то купил
    $('.nowbuy-close').on('click', function (e) {
        e.preventDefault();
        $('.modal-nowBuy').addClass('d-none');
        $.cookie('nowbuy_close', 1, {
            path: '/',
            expires: 24
        });
    });

    // формат ввода телефона
    $("form[name='forma_order'], input[name=returncall_mod_tel],input[name=tel],input[name=tel_new],input[name=oneclick_mod_tel]").on("click", function () {
        if (PHONE_FORMAT && PHONE_MASK) {
            $("input[name=tel_new], input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]").mask(PHONE_MASK);
        }
    });

    // Title
    $('.shop-page-main-title').text($('.page-title').text());

    // добавление в корзину подробное описание
    $("body").on('click', ".addToCartFull", function () {

        // Подтип
        if ($('#parentSizeMessage').html()) {

            // Размер
            if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                addToCartList($('input[name="parentSize"]:checked').val(), $('input[name="quant[2]"]').val(), $('input[name="parentSize"]:checked').attr('data-parent'));
                $(this).text(locale.incart);
            }
            // Размер  и цвет
            else if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {

                var color = $('input[name="parentColor"]:checked').attr('data-color');
                var size = $('input[name="parentSize"]:checked').attr('data-name');
                var parent = $('input[name="parentColor"]:checked').attr('data-parent');

                $.ajax({
                    url: ROOT_PATH + '/phpshop/ajax/option.php',
                    type: 'post',
                    data: 'color=' + escape(color) + '&parent=' + parent + '&size=' + escape(size),
                    dataType: 'json',
                    success: function (json) {
                        if (json['id'] > 0) {
                            if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {
                                addToCartList(json['id'], $('input[name="quant[2]"]').val(), $('input[name="parentColor"]:checked').attr('data-parent'));
                                $(this).text(locale.incart);
                            } else
                                showAlertMessage($('#parentSizeMessage').html());
                        }
                    }
                });
            } else
                showAlertMessage($('#parentSizeMessage').html());
        }
        // Опции характеристики
        else if ($('#optionMessage').html()) {
            var optionCheck = true;
            var optionValue = $('#allOptionsSet' + $(this).attr('data-uid')).val();
            $('.optionsDisp select').each(function () {
                if ($(this).hasClass('req') && optionValue === '')
                    optionCheck = false;
            });

            if (optionCheck) {
                addToCartList($(this).attr('data-uid'), $('input[name="quant[2]"]').val(), $(this).attr('data-uid'), optionValue);
                $(this).text(locale.incart);
            } else
                showAlertMessage($('#optionMessage').html());
        }
        // Обычный товар
        else {
            addToCartList($(this).attr('data-uid'), $('input[name="quant[1]"]').val());
            $(this).text(locale.incart);
        }

    });

    // выбор цвета 
    $('body').on('change', 'input[name="parentColor"]', function () {

        $('input[name="parentColor"]').each(function () {
            this.checked = false;
            $(this).parent('label').removeClass('label_active');
        });

        this.checked = true;
        $(this).parent('label').addClass('label_active');


        var color = $('input[name="parentColor"]:checked').attr('data-color');
        var size = $('input[name="parentSize"]:checked').attr('data-name');
        var parent = $('input[name="parentColor"]:checked').attr('data-parent');

        $.ajax({
            url: ROOT_PATH + '/phpshop/ajax/option.php',
            type: 'post',
            data: 'color=' + escape(color) + '&parent=' + parent + '&size=' + escape(size),
            dataType: 'json',
            success: function (json) {
                if (json['id'] > 0) {

                    // Смена цены
                    $('[itemprop="price"]').html(json['price']);

                    // Смена старой цены
                    if (json['price_n'] != "")
                        $('[itemscope] .price-old').html(json['price_n'] + '<span class="rubznak">' + $('[itemprop="priceCurrency"]').html() + '</span>');
                    else
                        $('[itemscope] .price-old').html('');

                    // Смена картинки
                    var parent_img = json['image_big'];
                    if (parent_img != "") {

                        $(".js-slide").each(function (index, el) {
                            if ($(this).attr('data-big-image') == parent_img) {
                                $('#heroSlider').slick('slickGoTo', index);
                            }

                        });
                    }

                    // Смена склада
                    $('#items').html(json['items']);

                }
            }
        });

    });

    // выбор размера
    $('body').on('change', 'input[name="parentSize"]', function () {
        var id = this.value;

        $('input[name="parentSize"]').each(function () {
            this.checked = false;
            $(this).parent('label').removeClass('label_active');
        });

        this.checked = true;
        $(this).parent('label').addClass('label_active');

        // Если нет цветов меняем сразу цену и картинку
        if ($('input[name="parentColor"]').val() === undefined) {

            // Смена цены
            $('[itemprop="price"]').html($(this).attr('data-price'));

            // Смена старой цены
            if ($(this).attr('data-priceold') != "")
                $('[itemscope] .price-old').html($(this).attr('data-priceold') + '<span class=rubznak>' + $('[itemprop="priceCurrency"]').html() + '</span>');
            else
                $('[itemscope] .price-old').html('');

            // Смена картинки
            var parent_img = $(this).attr('data-image');
            if (parent_img != "") {

                $(".js-slide").each(function (index, el) {
                    if ($(this).attr('data-big-image') == parent_img) {
                        $('#heroSlider').slick('slickGoTo', index);
                    }

                });
            }

            // Смена склада
            $('#items').html($(this).attr('data-items'));
        }

        $('.selectCartParentColor').each(function () {
            $(this).parent('label').removeClass('label_active');
            if ($(this).hasClass('select-color-' + id)) {
                $(this).parent('label').removeClass('not-active');
                $(this).parent('label').attr('title', $(this).attr('data-color'));

                $(this).val(id);
            } else {
                $(this).parent('label').addClass('not-active');
                $(this).parent('label').attr('title', 'Нет');
            }
        });
    });


    // plugin bootstrap minus and plus
    $('.btn-number').click(function (e) {
        e.preventDefault();

        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var input = $("input[name='" + fieldName + "']");
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if (type == 'minus') {

                if (currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change();
                }
                if (parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if (type == 'plus') {

                if (currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change();
                }
                if (parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
    });


    // Подсказки DaData.ru
    var DADATA_TOKEN = $('#body').attr('data-token');
    if (DADATA_TOKEN) {

        $('[name="name_new"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="name_person"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="oneclick_mod_name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="returncall_mod_name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[type="email"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "EMAIL",
            suggest_local: false,
            count: 5
        });
        $('[name="org_name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "PARTY",
            count: 5
        });
        $('[name="company"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "PARTY",
            count: 5
        });
    }

    //  Согласие на использование cookie
    $('.cookie-message a').on('click', function (e) {
        e.preventDefault();
        $.cookie('usecookie', 1, {
            path: '/',
            expires: 365
        });
        $(this).parent().slideToggle("slow");
    });
    var usecookie = $.cookie('usecookie');
    if (usecookie == undefined && COOKIE_AGREEMENT) {
        $('.cookie-message p').html(locale.cookie_message);
        $('.cookie-message').removeClass('d-none');
    }

    $('.rating-group .btn').bind(' mouseover click', function () {
        $(this).prevAll('.btn').addClass('hover');
        $(this).addClass('hover');
        $(this).nextAll('.btn').removeClass('hover');

    });

    $('.rating-group .btn').on('click', function () {
        $(this).addClass('active');
    });

    $('form[name="ajax-form"]').on('submit', function (e) {
        e.preventDefault();
        var modalId = $(this).attr('data-modal');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (data) {
                if (modalId) {
                    $('#' + modalId).modal('toggle');
                    $('#thanks-box ').modal('show');
                    $("#thanks-box .modal-body").html(data['message']);
                }
            }
        });
    });

// CAPTCHA
    $('body').on('click', '[data-toggle="modal"]', function () {

        // reCAPTCHA
        var recaptcha_oneclick = $($(this).attr('data-target')).find('#recaptcha_oneclick').get(0);
        var recaptcha_returncall = $($(this).attr('data-target')).find('#recaptcha_returncall').get(0);
        var recaptcha_notice = $($(this).attr('data-target')).find('#recaptcha_notice').get(0);
        var recaptcha_pricemail = $($(this).attr('data-target')).find('#recaptcha_pricemail').get(0);
        var recaptcha_review = $($(this).attr('data-target')).find('#recaptcha_review').get(0);
        var recaptcha_forma = $($(this).attr('data-target')).find('#recaptcha_forma').get(0);

        if (typeof recaptcha_oneclick !== "undefined" || typeof recaptcha_returncall !== "undefined" || typeof recaptcha_notice !== "undefined" || typeof recaptcha_pricemail !== "undefined" || typeof recaptcha_review !== "undefined" || typeof recaptcha_forma !== "undefined") {
            $.getScript("https://www.google.com/recaptcha/api.js?render=explicit")
                    .done(function () {
                        if (typeof grecaptcha !== "undefined") {

                            grecaptcha.ready(function () {
                                try {
                                    if (recaptcha_returncall)
                                        grecaptcha.render(recaptcha_returncall, {"sitekey": $(recaptcha_returncall).attr('data-key'), "size": $(recaptcha_returncall).attr('data-size')});
                                    if (recaptcha_oneclick)
                                        grecaptcha.render(recaptcha_oneclick, {"sitekey": $(recaptcha_oneclick).attr('data-key'), "size": $(recaptcha_oneclick).attr('data-size')});
                                    if (recaptcha_notice)
                                        grecaptcha.render(recaptcha_notice, {"sitekey": $(recaptcha_notice).attr('data-key'), "size": $(recaptcha_notice).attr('data-size')});
                                    if (recaptcha_pricemail)
                                        grecaptcha.render(recaptcha_pricemail, {"sitekey": $(recaptcha_pricemail).attr('data-key'), "size": $(recaptcha_pricemail).attr('data-size')});
                                    if (recaptcha_review)
                                        grecaptcha.render(recaptcha_review, {"sitekey": $(recaptcha_review).attr('data-key'), "size": $(recaptcha_review).attr('data-size')});
                                    if (recaptcha_forma)
                                        grecaptcha.render(recaptcha_forma, {"sitekey": $(recaptcha_forma).attr('data-key'), "size": $(recaptcha_forma).attr('data-size')});
                                } catch (e) {
                                }
                            });
                        }
                    });
        }

        // hCAPTCHA
        var hcaptcha_oneclick = $($(this).attr('data-target')).find('#hcaptcha_oneclick').get(0);
        var hcaptcha_returncall = $($(this).attr('data-target')).find('#hcaptcha_returncall').get(0);
        var hcaptcha_notice = $($(this).attr('data-target')).find('#hcaptcha_notice').get(0);
        var hcaptcha_pricemail = $($(this).attr('data-target')).find('#hcaptcha_pricemail').get(0);
        var hcaptcha_review = $($(this).attr('data-target')).find('#hcaptcha_review').get(0);
        var hcaptcha_forma = $($(this).attr('data-target')).find('#hcaptcha_forma').get(0);

        if (typeof hcaptcha_oneclick !== "undefined" || typeof hcaptcha_returncall !== "undefined" || typeof hcaptcha_notice !== "undefined" || typeof hcaptcha_pricemail !== "undefined" || typeof hcaptcha_review !== "undefined" || typeof hcaptcha_forma !== "undefined") {

            $.getScript("https://js.hcaptcha.com/1/api.js?render=explicit")
                    .done(function () {
                        if (typeof hcaptcha !== "undefined") {
                            try {
                                if (hcaptcha_returncall)
                                    hcaptcha.render(hcaptcha_returncall, {"sitekey": $(hcaptcha_returncall).attr('data-key'), "size": $(hcaptcha_returncall).attr('data-size')});
                                if (hcaptcha_oneclick)
                                    hcaptcha.render(hcaptcha_oneclick, {"sitekey": $(hcaptcha_oneclick).attr('data-key'), "size": $(hcaptcha_oneclick).attr('data-size')});
                                if (notice)
                                    hcaptcha.render(hcaptcha_notice, {"sitekey": $(hcaptcha_notice).attr('data-key'), "size": $(hcaptcha_notice).attr('data-size')});
                                if (pricemail)
                                    hcaptcha.render(hcaptcha_pricemail, {"sitekey": $(hcaptcha_pricemail).attr('data-key'), "size": $(hcaptcha_pricemail).attr('data-size')});
                                if (review)
                                    hcaptcha.render(hcaptcha_review, {"sitekey": $(hcaptcha_review).attr('data-key'), "size": $(hcaptcha_review).attr('data-size')});
                                if (forma)
                                    hcaptcha.render(hcaptcha_forma, {"sitekey": $(hcaptcha_forma).attr('data-key'), "size": $(hcaptcha_forma).attr('data-size')});
                            } catch (e) {
                            }

                        }
                    });
        }


        // Smartcaptcha
        var smartcaptcha_oneclick = $($(this).attr('data-target')).find('#smartcaptcha_oneclick').get(0);
        var smartcaptcha_returncall = $($(this).attr('data-target')).find('#smartcaptcha_returncall').get(0);
        var smartcaptcha_notice = $($(this).attr('data-target')).find('#smartcaptcha_notice').get(0);
        var smartcaptcha_pricemail = $($(this).attr('data-target')).find('#smartcaptcha_pricemail').get(0);
        var smartcaptcha_review = $($(this).attr('data-target')).find('#smartcaptcha_review').get(0);
        var smartcaptcha_forma = $($(this).attr('data-target')).find('#smartcaptcha_forma').get(0);

        if (typeof smartcaptcha_oneclick !== "undefined" || typeof smartcaptcha_returncall !== "undefined" || typeof smartcaptcha_notice !== "undefined" || typeof smartcaptcha_pricemail !== "undefined" || typeof smartcaptcha_review !== "undefined" || typeof smartcaptcha_forma !== "undefined") {

            $.getScript("https://smartcaptcha.yandexcloud.net/captcha.js")
                    .done(function () {
                        const widgetId = window.smartCaptcha.render(smartcaptcha_returncall, {
                            sitekey: $(smartcaptcha_returncall).attr('data-sitekey')
                        });
                    });

        }
    });
    
// Smartcaptcha
    if ($("#smartcaptcha_default").length) {
        $.getScript("https://smartcaptcha.yandexcloud.net/captcha.js")
                .done(function () {

                });
    }

// Recaptcha
    if ($("#recaptcha_default").length) {
        $.getScript("https://www.google.com/recaptcha/api.js?render=explicit")
                .done(function () {
                    if (typeof grecaptcha !== "undefined") {
                        grecaptcha.ready(function () {

                            if ($("#recaptcha_default").length)
                                grecaptcha.render("recaptcha_default", {"sitekey": $("#recaptcha_default").attr('data-key'), "size": $("#recaptcha_default").attr('data-size')});

                        });
                    }
                });
    }

// Hcaptcha
    if ($("#hcaptcha_default").length) {
        $.getScript("https://js.hcaptcha.com/1/api.js?render=explicit")
                .done(function () {
                    if (typeof hcaptcha !== "undefined") {
                        if ($("#hcaptcha_default").length) {
                            hcaptcha.render("hcaptcha_default", {"sitekey": $("#hcaptcha_default").attr('data-key'), "size": $("#hcaptcha_default").attr('data-size')});
                        }
                    }
                });
    }


    // Закрыть стикер в шапке
    $('.sticker-close').on('click', function (e) {
        e.preventDefault();
        $(".top-banner").remove();
        $.cookie('sticker_close', 1, {
            path: '/',
            expires: 365
        });
    });


    $(".swiper-container > .swiper-wrapper > div").addClass("swiper-slide");

    if ($(".swiper-container").length) {
        var swiper5 = new Swiper(".compare-slider", {
            slidesPerView: 3,
            speed: 800,
            nextButton: ".btn-next10",
            prevButton: ".btn-prev10",
            preventClicks: false,
            effect: "slide",

            preventClicksPropagation: false,
            breakpoints: {
                450: {
                    slidesPerView: 2
                },
                610: {
                    slidesPerView: 2
                },
                850: {
                    slidesPerView: 3
                },
                1000: {
                    slidesPerView: 4
                },
                1080: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 3
                },
                1500: {
                    slidesPerView: 3
                }
            }
        });
    }

    // Мобильный фильтр
    $('#mobile-filter').on('click', function (e) {
        e.preventDefault();
        $("#faset-filter-body").appendTo('#mobile-filter-wrapper');
    });

    // Повторная авторизация
    if ($('#usersError').html()) {
        $('input[name=login],input[name=password]').addClass('is-invalid');
        $('#signupModal').modal('show');
    }

});