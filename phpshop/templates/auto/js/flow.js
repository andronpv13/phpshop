$(document).on('ready', function () {
    // INITIALIZATION OF HEADER
    var header = new HSHeader($('#header')).init();

    // INITIALIZATION OF MEGA MENU
    var megaMenu = new HSMegaMenu($('.js-mega-menu'), {
        desktop: {
            position: 'left'
        }
    }).init();

    // INITIALIZATION OF UNFOLD
    var unfold = new HSUnfold('.js-hs-unfold-invoker').init();

    // INITIALIZATION OF UNFOLD SEARCH
    $('.js-hs-search-unfold-invoker').each(function () {
        var searchUnfold = new HSUnfold($(this), {
            afterOpen: function () {
                $('#searchSlideDownControl').focus();

            }
        }).init();
    });

    // INITIALIZATION OF FORM VALIDATION
    $('.js-validate').each(function () {
        $.HSCore.components.HSValidation.init($(this), {
            rules: {
                confirmPassword: {
                    equalTo: '#signupPassword'
                }
            }
        });
    });

    // INITIALIZATION OF SHOW ANIMATIONS
    $('.js-animation-link').each(function () {
        var showAnimation = new HSShowAnimation($(this)).init();
    });


    // INITIALIZATION OF SLICK CAROUSEL
    $('.js-slick-carousel').each(function () {
        var slickCarousel = $.HSCore.components.HSSlickCarousel.init($(this));
    });

    // INITIALIZATION OF GO TO
    $('.js-go-to').each(function () {
        var goTo = new HSGoTo($(this)).init();
    });

    // INITIALIZATION OF QUANTITY COUNTER
    $('.js-quantity-counter').each(function () {
        var quantityCounter = new HSQuantityCounter($(this)).init();
    });

    // INITIALIZATION OF SELECT2
    $('.js-custom-select').each(function () {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});