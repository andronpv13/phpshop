$().ready(function() {

    // Добавить в корзину товар - 1 шаг
    $('.cart-add').on('click', function(event) {
        event.preventDefault();

        var id = $(this).attr('data-id');

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});



        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: 'html',
            async: false,
            success: function(data) {
                //$('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#cart-add-send').remove();
                 $('#selectModal .modal-title').html(locale.add_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('cart-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-footer .cart-add-send').after('<input class=\'btn btn-sm btn-primary\' data-id=' + id + ' type=\'button\' id=\'cart-add-send\' value=\''+locale.ok+'\'>');
                $('#selectModal .modal-footer .cart-add-send').hide();
                $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
                $('#selectModal .modal-body').css('overflow-y', 'auto');
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
            }
        });
        $('.cart-list').hide();

    });


    // Добавить в корзину товар -  2 шаг
    $('body').on('click', '#cart-add-send', function(event) {
        var id = $(this).attr('data-id');



        $('.search-list input').each(function() {
            if (this.value > 0 || $(this).attr('data-cart') == 'true') {

                if ($(this).prop('checked')) {
                    $('input[name=\'productsgroup_products[' + id + '][id]\']').val($(this).attr('data-id'));
                    $('input[name=\'productsgroup_products[' + id + '][num]\']').val(1);
                }

            }
        });
        $('#selectModal').modal('hide');



    });

});