$(document).ready(function () {
    $('button[name="ymImportProducts"]').on('click', function (e) {
        e.preventDefault();

        if($('.ym-process-import').length > 0) {
            $('.ym-process-import').remove();
        }
        if($('.ym-info-container').length > 0) {
            $('.ym-info-container').remove();
        }

        $('.main').prepend('<div class="ym-info-container"></div>');
        $('.ym-info-container').prepend('<div class="alert alert-info" role="alert">Выполняется экспорт данных в Яндекс.Маркет. Пожалуйста, не закрывайте вкладку браузера до завершения операции.</div>');
        $('.ym-info-container').append('<div class="progress ym-process-import"><div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div></div>');

        ymImportData([], 1);
    });
});

function ymImportData(data, initial)
{
    var from = 0;
    if(data.hasOwnProperty('from')) {
        from = data.from;
    }
    var imported = 0;
    if(data.hasOwnProperty('imported')) {
        imported = data.imported;
    }
    var totalProducts = 0;
    if(data.hasOwnProperty('total_products')) {
        totalProducts = data.total_products;
    }

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/yandexcart/admpanel/ajax/admin.ajax.php',
        type: 'post',
        data: {
            from: from,
            imported: imported,
            total_products: totalProducts,
            initial: initial
        },
        dataType: "json",
        async: false,
        success: function(json) {
            if(json['success']) {
                if(json.hasOwnProperty('message')) {
                    $('.ym-info-container')
                        .append('<div class="alert alert-info" role="alert">' + json['message'] + '</div>');
                }
                if(json.hasOwnProperty('finished')) {
                    $('.ym-process-import .progress-bar')
                        .css('width', '100%')
                        .attr('aria-valuenow', 100)
                        .html('100%');
                    $('.ym-info-container')
                        .append('<div class="alert alert-success ym-message" role="alert">Данные успешно экспортированы.</div>');
                    setTimeout(function () {
                        $('.ym-info-container').remove();
                    }, 5000);
                } else {
                    $('.ym-process-import .progress-bar')
                        .css('width',  json['percent'] + '%')
                        .attr('aria-valuenow', json['percent'])
                        .html(json['percent'] + '%');

                    ymImportData(json, 0);
                }
            } else {
                $('.ym-process-import').remove();
                $('.ym-info-container')
                    .append('<div class="alert alert-danger" role="alert">' + json['message'] + '</div>');
            }
        }
    });
}