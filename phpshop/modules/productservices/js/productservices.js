$(document).ready(function(){
    $('body').on('change', '.product-service', function () {
        var price = Number($('.priceService').attr('content'));

        if($(this).prop('checked')) {
            price = price + Number($(this).attr('data-price'));
            $('.priceService').html(price.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')).attr('content', price);
        } else {
            price = price - Number($(this).attr('data-price'));
            $('.priceService').html(price.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')).attr('content', price);
        }
    });

    $("body").on('click', ".addToCartFull", function() {
        $('.product-service:checked').each(function (index, element) {
            // Подтип
            if ($('#parentSizeMessage').html()) {
                // Размер
                if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                    addToCartList($(element).attr('data-id'), 1);
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
                        success: function(json) {
                            if (json['id'] > 0) {
                                if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {
                                    addToCartList($(element).attr('data-id'), 1);
                                }
                            }
                        }
                    });
                }
            }
            // Опции характеристики
            else if ($('#optionMessage').html()) {
                var optionCheck = false;
                $('.select-option').each(function() {
                    if ($(this).hasClass('active'))
                        optionCheck = true;
                });

                if (optionCheck)
                    addToCartList($(element).attr('data-id'), 1);
            }
            // Обычный товар
            else {
                addToCartList($(element).attr('data-id'), 1);
            }
        });
    });
});