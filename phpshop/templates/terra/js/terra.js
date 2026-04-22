function searchOpen() {
    $('.search-open-button').on('click', function () {
        $('.search-big-block').addClass("active");
    });
    $('.search-close').on('click', function () {
        $('.search-big-block').removeClass("active");
        $('.header-search-form').trigger("reset");
    });
}
 /*
$(document).ajaxStop(function () {
   
    $('.newitems-list .product-col').matchHeight();
    $('.template-product-list .product-col').matchHeight();
    $('.caption h4').matchHeight();

    $('.price').matchHeight();
    setTimeout(function () {
        $('.stock').matchHeight();
        $('.product-img-centr').matchHeight();
    }, 600);
    $('.spec-list .product-colr').matchHeight();
    $('.nowbuy-list .product-col').matchHeight();
    $('.recomend_products .product-block').matchHeight();
})
*/
// -------------------- Slick slider (brands) --------------------

$(window).load(function () {

});

$(document).ready(function () {
    $('.brand-list').slick({
        slidesToShow: 8,
        slidesToScroll: 1,
        infinite: false,
        focusOnSelect: false,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            }
        ]
    });


    var pathname = self.location.pathname;
    //Р°РєС‚РёРІР°С†РёСЏ Р�?РµРЅСЋ
    $('.sidebar-nav > li').removeClass('dropdown');
    $('.sidebar-nav > li > ul').removeClass('dropdown-menu');
    $('.sidebar-nav > li > a').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).siblings('ul').removeClass('active');
        } else {
            $(this).addClass('active');
            $(this).siblings('ul').addClass('active');
            $(this).siblings('ul').addClass('fadeIn animated');
        }
    });
    $(".sidebar-nav li").each(function (index) {

        if ($(this).attr("data-cid") == pathname) {

            $(this).children("ul").addClass("active");
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

    setTimeout(function () {
        $('.brand-list').css('opacity', '1')
    }, 800);

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

    var body_width = $('body').width();
    if (body_width < 992) {
        $(".product-day-wrap").addClass("hide")
    }
    if ($(".product-day-wrap").hasClass("hide")) {
        $(".catalog-table-wrapper").css("width", "100%")
    }


    if ($(".carousel-inner .item+.item").length) {
        $(".carousel-control").css("visibility", "visible")
    }

    searchOpen();
    $('.visible-lg').each(function () {
        if ($(this).attr('style') == 'clear: both; width:100%')
            $(this).addClass('copyright');
    });


    $(window).on('scroll', function () {

        if ($(window).scrollTop() >= $('.header-top').offset().top) {
            //$('#main-menu').addClass('navbar-fixed-top');
            // toTop          
            $('#toTop').fadeIn();
        } else {
            //$('#main-menu').removeClass('navbar-fixed-top');
            $('#toTop').fadeOut();

        }
    });


    var pathname = self.location.pathname;

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

// -------------------- matchHeight --------------------

    $('.newitems-list .product-col').matchHeight();
    $('.template-product-list .product-col').matchHeight();
    $('.caption h4').matchHeight();
    $('.stock').matchHeight();
    $('.price').matchHeight();
    setTimeout(function () {
        $('.product-img-centr').matchHeight();
    }, 600);
    $('.spec-list .product-colr').matchHeight();
    $('.nowbuy-list .product-col').matchHeight();
    $('.recomend_products .product-block').matchHeight();


});


