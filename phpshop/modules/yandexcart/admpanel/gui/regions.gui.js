$(document).ready(function () {

    // Компания 1
    var currentRegion = Number($('input[name="yandex_region_id_new"]').val());
    if (currentRegion > 0) {
        $.ajax({
            mimeType: 'text/html; charset=windows-1251',
            url: '/phpshop/modules/yandexcart/admpanel/ajax/search_region.php',
            type: 'post',
            data: {
                id: currentRegion
            },
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success']) {
                    $('.yandex-region').removeClass('is-invalid').val(json['region']);
                } else {
                    $('.yandex-region').addClass('is-invalid').val();
                }
            }
        });
    }

    $('.yandex-region').autocomplete({
        source: "/phpshop/modules/yandexcart/admpanel/ajax/search_region.php",
        minLength: 2,
        autoFocus: true,
        response: function (event, ui) {
            $('.yandex-region').removeClass('is-invalid');
            if (ui.content.length === 0) {
                $('.yandex-region').addClass('is-invalid');
            }

            if (ui.content.length === 1) {
                $('.yandex-region').removeClass('is-invalid').val(ui.content[0].label);
                $('input[name="yandex_region_id_new"]').val(ui.content[0].value);
                $(".ui-autocomplete").hide();
            } else {
                $(".ui-autocomplete").show();
            }
        },
        select: function (event, ui) {
            event.preventDefault();
            $('.yandex-region').removeClass('is-invalid').val(ui.item.label);
            $('input[name="yandex_region_id_new"]').val(ui.item.value);
        },
        change: function (event, ui) {
            $('.yandex-region').removeClass('is-invalid');
            if (!ui.item) {
                $('input[name="yandex_region_id_new"]').val(0);
            }
        }
    });

    // Компания 2
    var currentRegion = Number($('input[name="yandex_region_id_2_new"]').val());
    if (currentRegion > 0) {
        $.ajax({
            mimeType: 'text/html; charset=windows-1251',
            url: '/phpshop/modules/yandexcart/admpanel/ajax/search_region.php',
            type: 'post',
            data: {
                id: currentRegion
            },
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success']) {
                    $('.yandex-region-2').removeClass('is-invalid').val(json['region']);
                } else {
                    $('.yandex-region-2').addClass('is-invalid').val();
                }
            }
        });
    }

    $('.yandex-region-2').autocomplete({
        source: "/phpshop/modules/yandexcart/admpanel/ajax/search_region.php",
        minLength: 2,
        autoFocus: true,
        response: function (event, ui) {
            $('.yandex-region-2').removeClass('is-invalid');
            if (ui.content.length === 0) {
                $('.yandex-region-2').addClass('is-invalid');
            }

            if (ui.content.length === 1) {
                $('.yandex-region-2').removeClass('is-invalid').val(ui.content[0].label);
                $('input[name="yandex_region_id_2_new"]').val(ui.content[0].value);
                $(".ui-autocomplete").hide();
            } else {
                $(".ui-autocomplete").show();
            }
        },
        select: function (event, ui) {
            event.preventDefault();
            $('.yandex-region-2').removeClass('is-invalid').val(ui.item.label);
            $('input[name="yandex_region_id_2_new"]').val(ui.item.value);
        },
        change: function (event, ui) {
            $('.yandex-region-2').removeClass('is-invalid');
            if (!ui.item) {
                $('input[name="yandex_region_id_2_new"]').val(0);
            }
        }
    });
    
    // Компания 3
    var currentRegion = Number($('input[name="yandex_region_id_3_new"]').val());
    if (currentRegion > 0) {
        $.ajax({
            mimeType: 'text/html; charset=windows-1251',
            url: '/phpshop/modules/yandexcart/admpanel/ajax/search_region.php',
            type: 'post',
            data: {
                id: currentRegion
            },
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success']) {
                    $('.yandex-region-3').removeClass('is-invalid').val(json['region']);
                } else {
                    $('.yandex-region-3').addClass('is-invalid').val();
                }
            }
        });
    }

    $('.yandex-region-3').autocomplete({
        source: "/phpshop/modules/yandexcart/admpanel/ajax/search_region.php",
        minLength: 2,
        autoFocus: true,
        response: function (event, ui) {
            $('.yandex-region-3').removeClass('is-invalid');
            if (ui.content.length === 0) {
                $('.yandex-region-3').addClass('is-invalid');
            }

            if (ui.content.length === 1) {
                $('.yandex-region-3').removeClass('is-invalid').val(ui.content[0].label);
                $('input[name="yandex_region_id_3_new"]').val(ui.content[0].value);
                $(".ui-autocomplete").hide();
            } else {
                $(".ui-autocomplete").show();
            }
        },
        select: function (event, ui) {
            event.preventDefault();
            $('.yandex-region-3').removeClass('is-invalid').val(ui.item.label);
            $('input[name="yandex_region_id_3_new"]').val(ui.item.value);
        },
        change: function (event, ui) {
            $('.yandex-region-3').removeClass('is-invalid');
            if (!ui.item) {
                $('input[name="yandex_region_id_3_new"]').val(0);
            }
        }
    });
});