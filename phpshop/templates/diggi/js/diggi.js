/** Ð?Ð·Ð¼ÐµÐ½Ð¸Ðµ Ð²Ð¸Ð´Ð° Ñ€ÐµÐ¹Ñ‚Ð¸Ð½Ð³Ð° Ñ‚Ð¾Ð²Ð°Ñ€Ð° Ð½Ð°Ñ‡Ð°Ð»Ð¾ **/
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
/** Ð?Ð·Ð¼ÐµÐ½Ð¸Ðµ Ð²Ð¸Ð´Ð° Ñ€ÐµÐ¹Ñ‚Ð¸Ð½Ð³Ð° Ñ‚Ð¾Ð²Ð°Ñ€Ð° ÐºÐ¾Ð½ÐµÑ† **/

/** Ð?Ð·Ð¼ÐµÐ½ÐµÐ½Ð¸Ðµ Ð²Ð¸Ð´Ð° Ñ€ÐµÐ¹Ñ‚Ð¸Ð½Ð³Ð° Ð¾Ñ‚Ð·Ñ‹Ð²Ð° Ð½Ð°Ñ‡Ð°Ð»Ð¾ **/
function changeOfReviewsRatingView() {
    var imgRaitingSrcZero = ('/phpshop/templates/diggi/images/stars/stars1-0.png')
    var imgRaitingSrcOne = ('/phpshop/templates/diggi/images/stars/stars1-1.png')
    var imgRaitingSrcTwo = ('/phpshop/templates/diggi/images/stars/stars1-2.png')
    var imgRaitingSrcThree = ('/phpshop/templates/diggi/images/stars/stars1-3.png')
    var imgRaitingSrcFour = ('/phpshop/templates/diggi/images/stars/stars1-4.png')
    var imgRaitingSrcFive = ('/phpshop/templates/diggi/images/stars/stars1-5.png')
    var raitingstarZero = ('<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>')
    var raitingstarOne = ('<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarTwo = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarThree = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
    var raitingstarFour = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
    var raitingstarFive = ('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');
    $('.comments-raiting-wrapper').each(function() {
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
/** Ð?Ð·Ð¼ÐµÐ½ÐµÐ½Ð¸Ðµ Ð²Ð¸Ð´Ð° Ñ€ÐµÐ¹Ñ‚Ð¸Ð½Ð³Ð° Ð¾Ñ‚Ð·Ñ‹Ð²Ð° ÐºÐ¾Ð½ÐµÑ† **/
$(document).ready(function() {
	   $('.phone').attr("autocomplete", "off");
    $('.phone').mask("+7 (999) 999-99-99");

$('.phone').on('keyup', function(event) {
    reserveVal = $(this).cleanVal();
    phone = $(this).cleanVal().slice(0,10);
    $(this).val($(this).masked(phone));
       if($(this).cleanVal()[1] == '9') {
          if($(this).cleanVal()[0] == '8' || $(this).cleanVal()[0] == '7') {
            phone = reserveVal.slice(1);
            $(this).val($(this).masked(phone)); 
          }
      }
		});	
	
	if ($(".carousel-inner .item+.item").length) {
		$(".carousel-control").css("visibility", "visible")
	}
	
    $(window).on('scroll', function() {

        if ($(window).scrollTop() >= $('.header-top').offset().top + 500) {
            $('#main-menu').addClass('navbar-fixed-top');
            // toTop          
            $('#toTop').fadeIn();
        } else {
            $('#main-menu').removeClass('navbar-fixed-top');
            $('#toTop').fadeOut();

        }
    });
    var previousScroll = 0,
            navBarOrgOffset = $('.navbar-collapse').offset().top;

    $(window).scroll(function() {
        var currentScroll = $(this).scrollTop();
        if (currentScroll > navBarOrgOffset) {
            if (currentScroll > previousScroll) {
                $('#main-menu').hide();
            } else {
                $('#main-menu').show();
                $('#main-menu').addClass('navbar-fixed-top');
            }
        } else {
            if (currentScroll < navBarOrgOffset) {
                $('#main-menu').removeClass('navbar-fixed-top');
            }
        }
        previousScroll = currentScroll;
    });


    changeOfProductRatingView();
    setInterval(changeOfReviewsRatingView, 100)
    $(document).on('click', function() {
        changeOfReviewsRatingView();
    })
    $('.sidebar-nav > li').removeClass('dropdown');
    $('.sidebar-nav > li > ul').removeClass('dropdown-menu');
    $('.sidebar-nav > li > a').on('click', function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).siblings('ul').removeClass('active');
        } else {
            $(this).addClass('active');
            $(this).siblings('ul').addClass('active');
            $(this).siblings('ul').addClass('fadeIn animated');
        }
    });
    $('.main-navbar-list-catalog-wrapper').children('li').children('ul').removeClass('dropdown-menu');
    $('.main-navbar-list-catalog-wrapper').children('li').children('ul').addClass('main-navbar-list-catalog-hidden');
    $('#nav-catalog-dropdown-link').on('click', function() {
        if ($('.main-navbar-list-catalog-wrapper').hasClass('open')) {
            $('.main-navbar-list-catalog-wrapper').removeClass('open');
            $('#nav-catalog-dropdown-link').removeClass('open');
            $('.main-navbar-list-catalog-wrapper').removeClass('fadeIn animated');
            //$('.main-navbar-list-catalog-wrapper').parents('.container').removeClass('border-fix')
            return false;
        } else {
            //$('.main-navbar-list-catalog-wrapper').parents('.container').addClass('border-fix')
            $('.main-navbar-list-catalog-wrapper').addClass('open');
            $('.main-navbar-list-catalog-wrapper').addClass('fadeIn animated');
            $('#nav-catalog-dropdown-link').addClass('open');
            $('.main-navbar-list-catalog-hidden').removeClass('active');
            return false;
        }
    });
    $('.main-navbar-list-catalog-wrapper > li > a').removeAttr('data-toggle data-hover data-delay aria-expanded');
    /*$('.main-navbar-list-catalog-wrapper > li > a').on('click', function() {
     if ($(this).hasClass('active')) {
     $(this).removeClass('active');
     $(this).siblings('ul').removeClass('active');
     $(this).siblings('ul').removeClass('fadeIn animated');
     } else {
     $(this).addClass('active');
     $(this).siblings('ul').addClass('active');
     $(this).siblings('ul').addClass('fadeIn animated');
     }
     });*/
    var pathname = self.location.pathname;
    //Ð°ÐºÑ‚Ð¸Ð²Ð°Ñ†Ð¸Ñ Ð¼ÐµÐ½ÑŽ
    $(".sidebar-nav li").each(function(index) {

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

    //Àêòèâàöèÿ ëåâîãî ìåíþ êàòàëîãà íà ñòðàíèöå ïðîäóêòà
    $('.breadcrumb > li > a').each(function() {
        var linkHref = $(this).attr('href');
        $('.sidebar-nav li').each(function() {
            if ($(this).attr('data-cid') == linkHref) {
                $(this).addClass("active");
                $(this).parent("ul").addClass("active");
                $(this).parent("ul").siblings('a').addClass("active");
                $(this).find("a").addClass("active");
            }
        });
        $('.sidebar-nav ul').each(function() {
            if ($(this).hasClass('active')) {
                $(this).parent('li').removeClass('active');
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

    //Àêòèâàöèÿ ñëàéäåðîâ íà ãëàâíîé ñòðàíèöå
    if (!$('.editor_var').length) {
        $('.spec-main-icon-slider > .swiper-wrapper .product-block-wrapper-fix').unwrap();
        $('.spec-main-icon-slider > .swiper-wrapper > div').addClass('swiper-slide');
        var swiper = new Swiper('.spec-main-icon-slider', {
            slidesPerView: 5,
            speed: 800,
            nextButton: '.btn-next1',
            prevButton: '.btn-prev1',
            preventClicks: false,
            effect: 'slide',
            preventClicksPropagation: false,
            breakpoints: {
	            520: {
	                slidesPerView: 2
	            },
                768: {
                    slidesPerView: 3
                },
                991: {
                    slidesPerView: 4
                },
                1200: {
                    slidesPerView: 4
                }
            }
        });

        $('.spec-main-slider > .swiper-wrapper .product-block-wrapper-fix').unwrap();
        $('.spec-main-slider > .swiper-wrapper > div').addClass('swiper-slide');
        var swiper = new Swiper('.spec-main-slider', {
            slidesPerView: 5,
            speed: 800,
            nextButton: '.btn-next2',
            prevButton: '.btn-prev2',
            preventClicks: false,
            effect: 'slide',
            preventClicksPropagation: false,
            breakpoints: {
            520: {
                slidesPerView: 2
            },
                768: {
                    slidesPerView: 3
                },
                991: {
                    slidesPerView: 4
                },
                1200: {
                    slidesPerView: 4
                }
            }
        });
    }
    $('.main-menu-button').on('click', function() {
        if ($('#main-menu').hasClass('main-menu-fix')) {
            $('#main-menu').removeClass('main-menu-fix fadeIn');
            $('body').removeClass('overflow-fix');
        } else {
            $('#main-menu').addClass('main-menu-fix fadeIn');
            $('body').addClass('overflow-fix');
        }
    });
    $('.nowBuy > .swiper-wrapper .product-block-wrapper-fix').unwrap();
    $('.nowBuy > .swiper-wrapper > div').addClass('swiper-slide');
    var swiper = new Swiper('.nowBuy', {
        slidesPerView: 5,
        speed: 800,
        nextButton: '.btn-next3',
        prevButton: '.btn-prev3',
        preventClicks: false,
        effect: 'slide',
        preventClicksPropagation: false,
        breakpoints: {
            520: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 3
            },
            991: {
                slidesPerView: 4
            },
            1200: {
                slidesPerView: 4
            }
        }
    });
	$('.navbar-brand img').remove()
	$(".compare-wrapper .swiper-container > .swiper-wrapper > div").addClass("swiper-slide");
		  var swiper5 = new Swiper(".compare-slider", {
    slidesPerView: 5,
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
	520: {
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
        slidesPerView: 4
      },
      1200: {
        slidesPerView: 4
      },
      1500: {
        slidesPerView: 5
      }
    }
  });
});
