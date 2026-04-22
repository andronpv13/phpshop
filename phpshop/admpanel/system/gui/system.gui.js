
$().ready(function() {

    var theme_new = false;
    
    // Промт-режим AI
    $('.yandexcloudModal').on('click', function (event) {
        event.preventDefault();
        $('#adminModal .modal-title').html(locale.help+' AI');
        $('#adminModal .glyphicon-fullscreen, #adminModal .glyphicon-eye-open').addClass('hidden');
        $('#adminModal .product-modal-content').attr('height', $(window).height() - 120);
        $('#adminModal .product-modal-content').attr('src', './system/ajax/yandexcloud.ajax.php');
        $('#adminModal').modal('show');
    });
   
    // Синхрнизация лицензии
    $("body").on('click', "#loadLic", function(event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_license
        }).done(function() {

            var data = [];
            data.push({name: 'loadLic', value: '1'});
            data.push({name: 'actionList[loadLic]', value: 'actionLoadLic.system.edit'});

            $.ajax({
                mimeType: 'text/html; charset='+locale.charset,
                url: '?path=system.about',
                data: data,
                type: 'post',
                dataType: "json",
                async: false,
                success: function(json) {
                    if (json['success'] == 1) {
                        window.location.reload();
                    } else
                        showAlertMessage(locale.license_update_false, true);
                }
            });
        })
    });

    // Активировать витрины
    $("body").on('click', ".select-action .activate", function(event) {
        event.preventDefault();
        var chk = $('input:checkbox:checked').length;
        var i = 0;

        if (chk > 0) {
            $('input:checkbox:checked').each(function() {
               
                var data = [];
                data.push({name: 'host_new', value: $(this).closest('.data-row').children('.host').text()});
                data.push({name: 'enabled_new', value: '1'});
                
                $('.status_edit_' + $(this).attr('data-id')).ajaxSubmit({
                    data: data,
                    dataType: "json",
                    success: function(json) {
                        if (json['success'] == 1) {
                            showAlertMessage(locale.save_done);
                        } else
                            showAlertMessage(locale.save_false, true);
                    }
                });

            });
        }
        else
            alert(locale.select_no);
    });

    // Настройка центрирования
    $('[name="option[watermark_center_enabled]"]').prop('checked', function(_, checked) {
        if (checked) {
            $('[name="option[watermark_right]"]').attr('disabled', true);
            $('[name="option[watermark_bottom]"]').attr('disabled', true);
        }
    });

    $('[name="option[watermark_center_enabled]"]').click(function() {
        $('[name="option[watermark_right]"]').attr('disabled', this.checked);
        $('[name="option[watermark_bottom]"]').attr('disabled', this.checked);
    });

    // Настройка почты
    $('[name="option[mail_smtp_enabled]"]').prop('checked', function(_, checked) {
        if (!checked) {
            $('[name="option[mail_smtp_auth]"]').attr('disabled', true);
            $('[name="option[mail_smtp_host]"]').attr('disabled', true);
            $('[name="option[mail_smtp_port]"]').attr('disabled', true);
            $('[name="option[mail_smtp_user]"]').attr('disabled', true);
            $('[name="option[mail_smtp_pass]"]').attr('disabled', true);
        }
    });

    $('[name="option[mail_smtp_enabled]"]').change(function() {
        var smtp_disabled = this.checked;
        $('[name="option[mail_smtp_auth]"]').attr('disabled', !smtp_disabled);
        $('[name="option[mail_smtp_host]"]').attr('disabled', !smtp_disabled);
        $('[name="option[mail_smtp_port]"]').attr('disabled', !smtp_disabled);
        $('[name="option[mail_smtp_user]"]').attr('disabled', !smtp_disabled);
        $('[name="option[mail_smtp_pass]"]').attr('disabled', !smtp_disabled);
    });

    // Настройка кэширования
    $('[name="option[filter_cache_enabled]"]').prop('checked', function(_, checked) {
        if (!checked) {
            $('[name="option[filter_cache_period]"]').attr('disabled', true);
        }
    });

    $('[name="option[filter_cache_enabled]"]').click(function() {
        var cache_disabled = this.checked;
        $('[name="option[filter_cache_period]"]').attr('disabled', !cache_disabled);
    });

    // Применение темы оформления
    $('#theme_new').on('changed.bs.select', function() {
        theme_new = true;
        var theme = $(this).val();
        ;

        $('#body').fadeOut('slow', function() {
            $('#bootstrap_theme').attr('href', './css/bootstrap-theme-' + theme + '.css');
            $('#body').fadeIn('slow');
        });
    });

    // Перезагрузка страницы при смене темы
    $("button[name=editID]").on('click', function(event) {
        event.preventDefault();
        if (theme_new === true) {
            setTimeout(function() {
                window.location.reload();
            }, 5000);
        }
    });

    // закрепление навигации
    if ($('#fix-check:visible').length && typeof(WAYPOINT_LOAD) != 'undefined')
        var waypoint = new Waypoint({
            element: document.getElementById('fix-check'),
            handler: function(direction) {
                $('.navbar-action').toggleClass('navbar-fixed-top');
            }
        });

    $(".tree a[data-view]").on('click', function(event) {
        event.preventDefault();

        $('html, body').animate({scrollTop: $("a[name=" + $(this).attr('data-view') + "]").offset().top - 100}, 500);
    });

    // Продление поддержки
    $(".pay-support").on('click', function(event) {
        event.preventDefault();
        $('[name=product_upgrade]').submit();
    });
    
    
    // Выбор captcha
    $('body').on('change', '[name="option[smartcaptcha_enabled]"]', function () {
         if ($(this).prop('checked') === true){
             $('[name="option[recaptcha_enabled]"]').bootstrapToggle('off');
             $('[name="option[hcaptcha_enabled]"]').bootstrapToggle('off');
         }
    });
    $('body').on('change', '[name="option[hcaptcha_enabled]"]', function () {
         if ($(this).prop('checked') === true){
             $('[name="option[recaptcha_enabled]"]').bootstrapToggle('off');
             $('[name="option[smartcaptcha_enabled]"]').bootstrapToggle('off');
         }
    });
    $('body').on('change', '[name="option[recaptcha_enabled]"]', function () {
         if ($(this).prop('checked') === true){
             $('[name="option[hcaptcha_enabled]"]').bootstrapToggle('off');
             $('[name="option[smartcaptcha_enabled]"]').bootstrapToggle('off');
         }
    });
    
});