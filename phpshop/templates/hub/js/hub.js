/** �?змение вида рейтинга товара начало **/
function changeOfProductRatingView() {
    var raitingWidth = $('#raiting_votes').outerWidth();
    var raitingstarZero = ('<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>')
    var raitingstarOne = ('<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarTwo = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarThree = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarFour = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
    var raitingstarFive = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');

    if (raitingWidth == 0) {
        $('#raiting_star').remove();
        $('.rating').append(raitingstarZero);
    }
    if (raitingWidth > 1 && raitingWidth <= 16) {
        $('#raiting_star').remove();
        $('.rating').append(raitingstarOne);
    }
    if (raitingWidth > 17 && raitingWidth <= 32) {
        $('#raiting_star').remove();
        $('.rating').append(raitingstarTwo);
    }
    if (raitingWidth > 33 && raitingWidth <= 48) {
        $('#raiting_star').remove();
        $('.rating').append(raitingstarThree);
    }
    if (raitingWidth > 49 && raitingWidth <= 64) {
        $('#raiting_star').remove();
        $('.rating').append(raitingstarFour);
    }
    if (raitingWidth > 65 && raitingWidth <= 80) {
        $('#raiting_star').remove();
        $('.rating').append(raitingstarFive);
    }
}
/** �?змение вида рейтинга товара конец **/

/** �?зменение вида рейтинга отзыва начало **/
function changeOfReviewsRatingView() {
    var imgRaitingSrcZero = ('/phpshop/templates/astero/images/stars/stars1-0.png')
    var imgRaitingSrcOne = ('/phpshop/templates/astero/images/stars/stars1-1.png')
    var imgRaitingSrcTwo = ('/phpshop/templates/astero/images/stars/stars1-2.png')
    var imgRaitingSrcThree = ('/phpshop/templates/astero/images/stars/stars1-3.png')
    var imgRaitingSrcFour = ('/phpshop/templates/astero/images/stars/stars1-4.png')
    var imgRaitingSrcFive = ('/phpshop/templates/astero/images/stars/stars1-5.png')
    var raitingstarZero = ('<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>')
    var raitingstarOne = ('<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarTwo = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarThree = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarFour = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
    var raitingstarFive = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');
    $('.comments-raiting-wrapper').each(function () {
        var imgRaitingSrc = $(this).children('img').attr('src');
        if ($(this).find('img')) {
            $(this).children('img').remove();
            if (imgRaitingSrc == imgRaitingSrcZero) {
                $(this).append(raitingstarZero);
            }
            if (imgRaitingSrc == imgRaitingSrcOne) {
                $(this).append(raitingstarOne);
            }
            if (imgRaitingSrc == imgRaitingSrcTwo) {
                $(this).append(raitingstarTwo);
            }
            if (imgRaitingSrc == imgRaitingSrcThree) {
                $(this).append(raitingstarThree);
            }
            if (imgRaitingSrc == imgRaitingSrcFour) {
                $(this).append(raitingstarFour);
            }
            if (imgRaitingSrc == imgRaitingSrcFive) {
                $(this).append(raitingstarFive);
            }
        }
    });
}
/** �?зменение вида рейтинга отзыва конец **/
$(document).ready(function () {

    $('.phone').attr("autocomplete", "off");
    $('.phone').mask("+7 (999) 999-99-99");

    $('.phone').on('keyup', function (event) {
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

    if ($(".carousel-inner .item+.item").length) {
        $(".carousel-control").css("visibility", "visible")
    }
    var body_width = $('body').width();
    if (body_width < 992) {
        $(".product-day-wrap").addClass("hide")
    }
    if ($(".product-day-wrap").hasClass("hide")) {
        $(".catalog-table-wrapper").css("width", "100%")
    }


    $(window).on('scroll', function () {

        if ($(window).scrollTop() >= $('.main-container, .slider').offset().top) {
            $('#main-menu').addClass('navbar-fixed-top')
            // toTop          
            $('#toTop').fadeIn();
        } else {
            $('#main-menu').removeClass('navbar-fixed-top');
            $('#toTop').fadeOut();

        }
    });


    $('.sidebar-nav li').removeClass('dropdown');
    $('.sidebar-nav li ul').removeClass('dropdown-menu');

    $('.main-navbar-list-catalog-wrapper').children('li').children('ul').removeClass('dropdown-menu');
    $('.main-navbar-list-catalog-wrapper').children('li').children('ul').addClass('main-navbar-list-catalog-hidden');
    $('#nav-catalog-dropdown-link').on('click', function () {
        if ($('.main-navbar-list-catalog-wrapper').hasClass('open')) {
            $('.main-navbar-list-catalog-wrapper').removeClass('open');
            $('#nav-catalog-dropdown-link').removeClass('open');
            $('.main-navbar-list-catalog-wrapper').removeClass('fadeIn animated');
        } else {
            $('.main-navbar-list-catalog-wrapper').addClass('open');
            $('.main-navbar-list-catalog-wrapper').addClass('fadeIn animated');
            $('#nav-catalog-dropdown-link').addClass('open');
            $('.main-navbar-list-catalog-hidden').removeClass('active');
        }
    });

    var pathname = self.location.pathname;
    //активация меню
    $(".sidebar-nav li").each(function (index) {

        if ($(this).attr("data-cid") == pathname) {
            var cid = $(this).attr("data-cid-parent");
            $("#cid" + cid).addClass("active");
            $("#cid" + cid).attr("aria-expanded", "false");
            $("#cid-ul" + cid).addClass("active");
            $(this).addClass("active");
            $(this).parent("ul").addClass("active");
            $(this).parent("ul").siblings('a').addClass("active");
        }
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

    //��������� ������ ���� �������� �� �������� ��������
    $('.breadcrumb > li > a').each(function () {
        var linkHref = $(this).attr('href');
        $('.sidebar-nav li').each(function () {
            if ($(this).attr('data-cid') == linkHref) {
                $(this).addClass("active");
                $(this).parent("ul").addClass("active");
                $(this).parent("ul").siblings('a').addClass("active");
            }
        });
        $('.sidebar-nav ul').each(function () {
            if ($(this).hasClass('active')) {
                $(this).parent('li').removeClass('active');
            }
        });
    });
    //$('.main-navbar-list-catalog-wrapper > li > a').removeAttr('data-toggle data-hover data-delay aria-expanded');

    $('.main-menu-button').on('click', function () {
        if ($('#main-menu').hasClass('main-menu-fix')) {
            $('#main-menu').removeClass('main-menu-fix');
            $('body').removeClass('overflow-fix');
        } else {
            $('#main-menu').addClass('main-menu-fix');
            $('body').addClass('overflow-fix');
        }
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
                    slidesPerView: 1
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
});
