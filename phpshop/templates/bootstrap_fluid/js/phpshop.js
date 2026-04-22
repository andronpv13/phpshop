
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
                $("#num, #mobilnum").html(json['num']);
                $("#sum").html(json['sum']);
                $("#bar-cart, #order").addClass('active');
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
                $("#numcompare").html(json['num']);
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

// оформление кнопок
$(".ok").addClass('btn btn-default btn-sm');
$("input:button").addClass('btn btn-default btn-sm');
$("input:submit").addClass('btn btn-primary');
$("input:text,input:password, textarea").addClass('form-control');


// Активная кнопка
function ButOn(Id) {
    Id.className = 'imgOn';
}

function ButOff(Id) {
    Id.className = 'imgOff';
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

// Проверка наличия файла картинки, прячем картинку
function NoFoto2(obj) {
    obj.height = 0;
    obj.width = 0;
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

                // Выравнивание ячеек товара
                setEqualHeight(".product-description");
                setEqualHeight(".product-name-fix");
               
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
               
                // lazyLoad
                setTimeout(function () {
                    $(window).lazyLoadXT();
                }, 50);

                $("#pagination-block").html(data['pagination']);

                // Сброс Waypoint
                Waypoint.refreshAll();
            }
        },
        error: function (data) {
            $(obj).attr('checked', false);
            //$(obj).attr('disabled', true);

            if ($(obj).attr('name') == 'max')
                $('#price-filter-val-max').addClass('has-error');
            if ($(obj).attr('name') == 'min')
                $('#price-filter-val-min').addClass('has-error');

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
            if (hashes == '#')
                history.pushState('', document.title, window.location.pathname);
            else
                window.location.hash = hashes;
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

// Выравнивание ячеек товара
function setEqualHeight(columns) {

    $(columns).closest('.row ').each(function () {
        var tallestcolumn = 0;

        $(this).find(columns).each(function () {
            var currentHeight = $(this).height();
            if (currentHeight > tallestcolumn) {
                tallestcolumn = currentHeight;
            }
        });

        if (tallestcolumn > 0) {
            $(this).find(columns).height(tallestcolumn);
        }
    });

}
function mainNavMenuFix() {
    var body_width = $('body').width();
    if (body_width < 768) {
        $('.mobile-menu .sub-marker').removeClass('sub-marker')


    }

    if (body_width > 767) {
        var nav_weight = $('.main-navbar-top').width();
        var full_weight = 0;
        $('.main-navbar-top > li').each(function () {
            full_weight += $(this).width();
        });
        var menu_content = ('<div class="additional-nav-menu"><a href="#" class="dropdown-toggle link" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i></a><ul class="dropdown-menu dropdown-menu-right aditional-link" role="menu"></ul></div>');
        if ($('.header-menu-wrapper').find('.additional-nav-menu')) {
            var nav_weight_fix = nav_weight - 46;
        }
        if (nav_weight < full_weight) {
            var nav_weight_fix = nav_weight - 46;
            if ($('.header-menu-wrapper').find('.additional-nav-menu')) {
                $('.header-menu-wrapper > .row').append(menu_content);
            }

            while (nav_weight_fix < full_weight) {
                $('.main-navbar-top > li:last-child').appendTo('.aditional-link');
                var full_weight = 0;
                $('.main-navbar-top > li').each(function () {
                    full_weight += $(this).width();
                });
            }

        }
        /* $('.main-navbar-top').addClass('active');*/
    }
}
function productPageSliderImgFix() {
    var block_height = $('.bx-wrapper .bx-viewport').height();
    var block_height_fix = block_height + 'px';
    $('.bx-wrapper .bx-viewport .bxslider > div > a').css('line-height', block_height_fix);

}


var body_height = $(window).height() - 200;
var body_height2 = $(window).height() - 330;
$(document).ajaxStop(function () {

    setTimeout(function () {
        //setEqualHeight(".thumbnail");
        //setEqualHeight(".caption img");
    }, 600);
    setEqualHeight(".caption h5");
    setEqualHeight(".thumbnail .description");
    setEqualHeight(".prod-photo");
    setEqualHeight(".product-price");
    setEqualHeight(".stock");
});
$(document).ready(function () {
    $('.navbar-toggle').on('click', function () {
        $('body').toggleClass('overflow')
    })


    $('.sidebar-nav > li').removeClass('dropdown');
    $('.sidebar-nav > li > ul').removeClass('dropdown-menu');

    $('.sidebar-nav  li  a').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).siblings('ul').removeClass('active');
        } else {

            $(this).addClass('active');
            $(this).siblings('ul').addClass('active');
            $(this).siblings('ul').removeClass('fadeIn animated');
        }
    });
    var pathname = self.location.pathname;
    //Р°РєС‚РёРІР°С†РёСЏ РјРµРЅСЋ
    $(".sidebar-nav li").each(function (index) {

        if ($(this).attr("data-cid") == pathname) {
            $(this).children("ul").addClass("active");
            var cid = $(this).attr("data-cid-parent");
            $("#cid" + cid).addClass("active");
            $("#cid" + cid).attr("aria-expanded", "false");
            $("#cid-ul" + cid).addClass("active");
            $(this).addClass("active");
            $(this).parents("ul").addClass("active");
            $(this).parents("ul").siblings('a').addClass("active");
            $(this).find("a").addClass("active");
        }
    });

    //Активация левого меню каталога на странице продукта
    $('.breadcrumb > li > a').each(function () {
        var linkHref = $(this).attr('href');
        $('.sidebar-nav li').each(function () {
            if ($(this).attr('data-cid') == linkHref) {
                $(this).addClass("active");
                $(this).parent("ul").addClass("active");
                $(this).parent("ul").siblings('a').addClass("active");
                $(this).find("a").addClass("active");
            }
        });
        $('.sidebar-nav ul').each(function () {
            if ($(this).hasClass('active')) {
                $(this).parent('li').removeClass('active');
            }
        });
    });
    //mainNavMenuFix();
    $(".filter-btn").on('click', function () {

        $("#faset-filter").fadeIn();


    });
    $(".filter-close").click(function () {
        $("#faset-filter").fadeOut();

    });


    $(".faset-filter-name .close").on('click', function () {
        $("#faset-filter").fadeOut();


    });
    if ($(".carousel-inner .item+.item").length) {

        $(".carousel-control, .carousel-indicators").css("visibility", "visible")
    }

    /*
     setTimeout(function() {
     $('input[name="tel_new"]').mask("+7 (999) 999-99-99");
     
     $('input[name="tel_new"]').on('keyup', function(event) {
     reserveVal = $(this).cleanVal();
     phone = $(this).cleanVal().slice(0, 10);
     $(this).val($(this).masked(phone));
     if ($(this).cleanVal()[1] == '9') {
     if ($(this).cleanVal()[0] == '8' || $(this).cleanVal()[0] == '7') {
     phone = reserveVal.slice(1);
     $(this).val($(this).masked(phone));
     }
     }
     });
     
     }, 1000);
     */

    var pathname = self.location.pathname;

    $(".left-block-list  li").each(function (index) {

        if ($(this).attr("data-cid") == pathname) {
            $(this).children("ul").addClass("active");
            $(this).find("i").toggleClass("fa-chevron-down")
            $(this).find("i").toggleClass("fa-chevron-up")
            var cid = $(this).attr("data-cid-parent");

            $("#cid" + cid).addClass("active");
            $("#cid" + cid).attr("aria-expanded", "false");
            $("#cid-ul" + cid).addClass("active");
            $(this).addClass("active");
            $(this).parent("ul").addClass("active");
            $(this).parent("ul").siblings('a').addClass("active");
            $(this).find("a").addClass("active");

        }
    });

    $('.left-block-list > li').removeClass('dropdown');

    $('.left-block-list > li > ul').removeClass('dropdown-menu');
    $('.left-block-list > li > a').on('click', function () {
        $(this).find("i").toggleClass("fa-chevron-down")
        $(this).find("i").toggleClass("fa-chevron-up")
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).siblings('ul').removeClass('active');
        } else {
            $(this).addClass('active');
            $(this).siblings('ul').addClass('active');
            $(this).siblings('ul').addClass('fadeIn animated');
        }
    });

    //Активация левого меню каталога на странице продукта
    $('.breadcrumb > li > a').each(function () {
        var linkHref = $(this).attr('href');
        $('.left-block li').each(function () {
            if ($(this).attr('data-cid') == linkHref) {
                $(this).addClass("active");
                $(this).parent("ul").addClass("active");
                $(this).parent("ul").siblings('a').addClass("active");
                $(this).find("a").addClass("active");
            }
        });
        $('.left-block ul').each(function () {
            if ($(this).hasClass('active')) {
                $(this).parent('li').removeClass('active');
            }
        });
    });
    /*$(document).mouseup(function (e) {
     var container = $('.popover');
     if (container.has(e.target).length === 0){
     container.hide();
     }
     });*/
    $("#cartlink").popover({
        delay: {show: 0, hide: 800}
    });


    setTimeout(function () {
        $('.header-menu-wrapper li').removeClass('active');
        $('.header-menu-wrapper').css("opacity", "1")
        $('.header-menu-wrapper').css("height", "auto")
    }, 400);
    // логика кнопки оформления заказа 
    $("button.orderCheckButton").on("click", function (e) {
        e.preventDefault();
        OrderChekJq();
    });

    // Выравнивание ячеек товара
    setEqualHeight(".thumbnail .description");
    setEqualHeight(".prod-photo");

    setTimeout(function () {
        //   setEqualHeight(".thumbnail");
        setEqualHeight(".stock");
        //setEqualHeight(".caption img");

    }, 900);




    setEqualHeight(".caption h5");
    // Корректировка стилей меню
    $('.mega-more-parent').each(function () {
        if ($(this).hasClass('hide') || $(this).hasClass('hidden'))
            $(this).prev().removeClass('template-menu-line');
    });

    $(".swiper-container > .swiper-wrapper > div").addClass("swiper-slide");

    if ($(".swiper-container").length)
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
    setEqualHeight(".prod-title");
    setEqualHeight(".prod-photo");
    setEqualHeight(".product-name");


    setEqualHeight(".prod-desc");
    setEqualHeight(".prod-sort");
    // Вывод всех категорий в мегаменю
    $('.mega-more').on('click', function (event) {
        event.preventDefault();
        $(this).hide();
        $(this).closest('.mega-menu-block').find('.template-menu-line').removeClass('hide');
    });


    // Направление сортировки в брендах
    $('#filter-selection-well input:radio').on('change', function () {
        window.location.href = $(this).attr('data-url');
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
        $("#faset-filter").removeClass('hide');
        $('.filter-btn').addClass('visible-filter')
    } else {
        $("#faset-filter").hide();
    }

    if (!FILTER) {
        $("#faset-filter").hide();
        $("#sorttable").removeClass('hide');
    }


    // Направление сортировки
    $('#filter-well input:radio').on('change', function () {
        if (AJAX_SCROLL) {

            count = current;
            var hashes = window.location.hash;
            hashes = hashes.split('s=1&').join('');
            hashes = hashes.split('s=2&').join('');
            hashes = hashes.split('s=3&').join('');
            hashes = hashes.split('f=2&').join('');
            hashes = hashes.split('f=1&').join('');
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
        }

    });


    // Пагинация товаров
    $('.pagination a').on('click', function (event) {
        if (AJAX_SCROLL) {
            event.preventDefault();
            window.location.href = $(this).attr('href') + window.location.hash;
        }
    });


    // toTop
    $('#toTop').on('click', function (event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: $("header").offset().top - 100}, 500);
    });

    // закрепление навигации
    $('.main-container').waypoint(function () {
        if (FIXED_NAVBAR)
            $('#navigation').toggleClass('navbar-fixed-top');

        // toTop          
        $('#toTop').fadeToggle();
    });

    // быстрый переход
    $(document).on('keydown', function (e) {
        if (e == null) { // ie
            key = event.keyCode;
            var ctrl = event.ctrlKey;
        } else { // mozilla
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

    // подгрузка комментариев
    $("body").on('click', '#commentLoad', function () {
        commentList($(this).attr('data-uid'), 'list');
    });

    // убираем пустые закладки подробного описания
    if ($('#files').html() != 'Нет файлов')
        $('#filesTab').addClass('show');

    if ($('#vendorenabled').html() != '')
        $('#settingsTab').addClass('show');

    if ($('#pages').html() != '')
        $('#pagesTab').addClass('show');


    // Иконки в основном меню категорий
    if (MEGA_MENU_ICON === false) {
        $('.mega-menu-block img').hide();
    }


    // убираем меню брендов
    if (BRAND_MENU === false) {
        $('#brand-menu').hide();
    }

    if (CATALOG_MENU === false) {
        $('#catalog-menu').hide();
    } else {
        $('#catalog-menu').removeClass('hide');
    }

    // добавление в корзину
    $('body').on('click', 'button.addToCartList', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'));
        $(this).attr('disabled', 'disabled');
        $(this).addClass('btn-success');
        $(this).text(locale.incart);
        $('#order').addClass('active');
    });

    // изменение количества товара для добавления в корзину
    $('body').on('change', '.addToCartListNum', function () {
        var num = (Number($(this).val()) || 1);
        var id = $(this).attr('data-uid');
        /*
         if (num > 0 && $('.addToCartList').attr('data-uid') === $(this).attr('data-uid'))
         $('.addToCartList').attr('data-num', num);*/
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
    $('body').on('click', '.addToWishList', function () {
        addToWishList($(this).attr('data-uid'));
    });

    // добавление в compare
    $('body').on('click', '.addToCompareList', function () {
        addToCompareList($(this).attr('data-uid'));
    });

    // отправка сообщения администратору из личного кабинета
    $("#CheckMessage").on('click', function () {
        if ($("#message").val() != '')
            $("#forma_message").submit();
    });

    // Визуальная корзина
    if ($("#cartlink").attr('data-content') == "") {
        $("#cartlink").attr('href', ROOT_PATH+'/order/');
    }
    $('[data-toggle="popover"]').popover();
    $('a[data-toggle="popover"]').on('show.bs.popover', function () {
        $('a[data-toggle="popover"]').attr('data-content', $("#visualcart_tmp").html());
    });

    // Подсказки 
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

    // Стилизация select
    $('.selectpicker').selectpicker({
        width: "100%"
    });

    // Переход из прайса на форму с описанием
    $('#price-form').on('click', function (event) {
        event.preventDefault();
        if ($(this).attr('data-uid') != "" && $(this).attr('data-uid') != "ALL")
            window.location.replace("../shop/CID_" + $(this).attr('data-uid') + ".html");
    });

    // Ajax поиск
    $("#search").on('input', function () {
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

                        if (data != $("#search").attr('data-content')) {
                            $("#search").attr('data-content', data);

                            $("#search").popover('show');
                        }
                    } else
                        $("#search").popover('hide');
                }
            });
        } else {
            $("#search").attr('data-content', '');
            $("#search").popover('hide');

        }
    });

    // Повторная авторизация
    if ($('#usersError').html()) {
        $('form[name=user_forma] .form-group').addClass('has-error has-feedback');
        $('form[name=user_forma] .glyphicon').removeClass('hide');
        $('#userModal').modal('show');
        $('#userModal').on('shown.bs.modal', function () {

        });
    }

    // Проверка синхронности пароля регистрации
    $("form[name=user_forma_register] input[name=password_new2]").on('blur', function () {
        if ($(this).val() != $("form[name=user_forma_register] input[name=password_new]").val()) {
            $('form[name=user_forma_register] #check_pass').addClass('has-error has-feedback');
            $('form[name=user_forma_register] .glyphicon').removeClass('hide');
        } else {
            $('form[name=user_forma_register] #check_pass').removeClass('has-error has-feedback');
            $('form[name=user_forma_register] .glyphicon').addClass('hide');
        }
    });

    // Регистрация пользователя
    $("form[name=user_forma_register]").on('submit', function () {
        if ($(this).find("input[name=password_new]").val() != $(this).find("input[name=password_new2]").val()) {
            $(this).find('#check_pass').addClass('has-error has-feedback');
            $(this).find('.glyphicon').removeClass('hide');
            return false;
        } else
            $(this).submit();
    });

    // Ошибка регистрации
    if ($("#user_error").html()) {
        $("#user_error").find('.list-group-item').addClass('list-group-item-warning');
    }

    // формат ввода телефона
    $("form[name='forma_order'], input[name=returncall_mod_tel],input[name=tel],input[name=tel_new],input[name=oneclick_mod_tel]").on("click", function () {
        if (PHONE_FORMAT && PHONE_MASK) {
            $("input[name=tel_new], input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]").mask(PHONE_MASK);
        }
    });

    /*
     setTimeout(function() {
     $('input[name=tel_new]').mask("+7 (999) 999-99-99");
     
     $('input[name=tel_new]').on('keyup', function(event) {
     reserveVal = $(this).cleanVal();
     phone = $(this).cleanVal().slice(0, 10);
     $(this).val($(this).masked(phone));
     if ($(this).cleanVal()[1] == '9') {
     if ($(this).cleanVal()[0] == '8' || $(this).cleanVal()[0] == '7') {
     phone = reserveVal.slice(1);
     $(this).val($(this).masked(phone));
     }
     }
     });
     }, 2500);
     $('input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]').mask("+7 (999) 999-99-99");
     
     $('input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]').on('keyup', function(event) {
     reserveVal = $(this).cleanVal();
     phone = $(this).cleanVal().slice(0, 10);
     $(this).val($(this).masked(phone));
     if ($(this).cleanVal()[1] == '9') {
     if ($(this).cleanVal()[0] == '8' || $(this).cleanVal()[0] == '7') {
     phone = reserveVal.slice(1);
     $(this).val($(this).masked(phone));
     }
     }
     })
     */

    // Фотогалерея в по карточке товара
    if ($('.bxslider').length) {
        $('.bxslider-pre').addClass('hide');
        $('.bxslider').removeClass('hide');
        slider = $('.bxslider').bxSlider({
            mode: 'fade',
            pagerCustom: '.bx-pager'
        });
    }
    productPageSliderImgFix();

    // Фотогалерея в по карточке товара с большими изображениями
    $(document).on('click', '.bxslider a', function (event) {
        event.preventDefault();
        $('#sliderModal').modal('show');
        $('.bxsliderbig').html($('.bxsliderbig').attr('data-content'));
        $('.modal .modal-body img').css("max-height", $(window).height() * 0.65);
        setTimeout(function () {



            $('.modal .modal-body .bx-viewport').css("max-height", $(window).height() * 0.65);
            $('.modal .modal-body .bx-viewport').css("opacity", "1")

        }, 600);
        sliderbig = $('.bxsliderbig').bxSlider({
            mode: 'fade',
            pagerCustom: '.bx-pager-big'
        });


        if ($('.bx-pager-big').length == 0) {
            $('.modal-body').append('<div class="bx-pager-big">' + $('.bxsliderbig').attr('data-page') + '</div>');
            sliderbig.reloadSlider();
        }

        sliderbig.goToSlide(slider.getCurrentSlide());

    });

    // Закрытие модального окна фотогарелерии, клик по изображению
    $(document).on('click', '.bxsliderbig a', function (event) {
        event.preventDefault();
        slider.goToSlide(sliderbig.getCurrentSlide());
        $('#sliderModal').modal('hide');
    });

    // Закрытие модального окна фотогарелерии
    $('#sliderModal').on('hide.bs.modal', function () {
        slider.goToSlide(sliderbig.getCurrentSlide());
        sliderbig.destroySlider();
        delete sliderbig;
    });

    // Сворачиваемый блок 
    $('.collapse').on('show.bs.collapse', function () {
        $(this).prev('h4').find('i').removeClass('icon-caret-right icn-right');
        $(this).prev('h4').find('i').addClass('icon-caret-down icn-down ');
        $(this).prev('h4').attr('title', locale.hide);
    });
    $('.collapse').on('hidden.bs.collapse', function () {
        $(this).prev('h4').find('i').removeClass('icon-caret-down icn-down');
        $(this).prev('h4').find('i').addClass('icon-caret-right icn-right');
        $(this).prev('h4').attr('title', locale.show);
    });


    // добавление в корзину подробное описание
    $("body").on('click', ".addToCartFull", function () {

        // Подтип
        if ($('#parentSizeMessage').html()) {

            // Размер
            if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                addToCartList($('input[name="parentSize"]:checked').val(), $('input[name="quant[2]"]').val(), $('input[name="parentSize"]:checked').attr('data-parent'));
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
                            if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0)
                                addToCartList(json['id'], $('input[name="quant[2]"]').val(), $('input[name="parentColor"]:checked').attr('data-parent'));
                            else
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

            if (optionCheck)
                addToCartList($(this).attr('data-uid'), $('input[name="quant[2]"]').val(), $(this).attr('data-uid'), optionValue);
            else
                showAlertMessage($('#optionMessage').html());
        }
        // Обычный товар
        else {
            addToCartList($(this).attr('data-uid'), $('input[name="quant[1]"]').val());
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

                        $(".bx-pager img").each(function (index, el) {
                            if ($(this).attr('data-big-image') == parent_img) {
                                slider.goToSlide(index);
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

                $(".bx-pager img").each(function (index, el) {
                    if ($(this).attr('src') == parent_img) {
                        slider.goToSlide(index);
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

    // FlipClock
    if ($('.clock').length) {
        var now = new Date();
        var night = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
                $('.clock').attr('data-hour'), 0, 0
                );
        var msTillMidnight = night.getTime() / 1000 - now.getTime() / 1000;
        var clock = $('.clock').FlipClock({
            language: 'russian',
            coundown: true

        });
        clock.setTime(msTillMidnight);
        clock.setCountdown(true);
        clock.start();
    }

    // plugin bootstrap minus and plus http://jsfiddle.net/laelitenetwork/puJ6G/
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

    $('.odnotip .col-md-12').addClass("col-md-4 col-sm-3 col-xs-6")
    $('.odnotip .col-md-12').removeClass("col-md-12")

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
        $('.cookie-message').removeClass('hide');
    }


    //mobile-menu 
    $('.mobile-menu .sub-marker').on('click', function () {
      window.location.href = $(this).attr('href');
    });

/*
    $(window).resize(function () {
        //mainNavMenuFix();
    })*/

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

     // Закрыть стикер в шапке
    $('.sticker-close').on('click', function (e) {
        e.preventDefault();
        $(".top-banner").remove();
        $.cookie('sticker_close', 1, {
            path: '/',
            expires: 365
        });
    });
});


// Recaptcha
if ($("#recaptcha_default").length || $("#recaptcha_returncall").length) {

    $.getScript("https://www.google.com/recaptcha/api.js?render=explicit")
            .done(function () {
                if (typeof grecaptcha !== "undefined") {
                    grecaptcha.ready(function () {
                        if ($("#recaptcha_default").length)
                            grecaptcha.render("recaptcha_default", {"sitekey": $("#recaptcha_default").attr('data-key'), "size": $("#recaptcha_default").attr('data-size')});
                        
                        if ($("#recaptcha_returncall").length)
                            grecaptcha.render("recaptcha_returncall", {"sitekey": $("#recaptcha_returncall").attr('data-key'), "size": $("#recaptcha_returncall").attr('data-size')});

                    });
                }
            });
}

// Hcaptcha
if ($("#hcaptcha_default").length || $("#hcaptcha_returncall").length) {
    $.getScript("https://js.hcaptcha.com/1/api.js?render=explicit")
            .done(function () {
                if (typeof hcaptcha !== "undefined") {
                    if ($("#hcaptcha_default").length)
                        hcaptcha.render("hcaptcha_default", {"sitekey": $("#hcaptcha_default").attr('data-key'), "size": $("#hcaptcha_default").attr('data-size')});
                    if ($("#hcaptcha_returncall").length)
                        hcaptcha.render("hcaptcha_returncall", {"sitekey": $("#hcaptcha_returncall").attr('data-key'), "size": $("#hcaptcha_returncall").attr('data-size')});
                }
            });
}

