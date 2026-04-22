function yandexwidgetStart() {
    $('input[name="yadelivery_sum"]').remove();
    $('input[name="yadelivery_info"]').remove();
    $('input[name="yadelivery_type"]').remove();
    $('input[name="yadelivery_pvz_id"]').remove();
    $('input[name="yadelivery_address"]').remove();
    $('input[name="yadelivery_city_id"]').remove();
    $('input[name="yadelivery_tariff"]').remove();


    $('<input type="hidden" name="yadelivery_sum">').insertAfter('#dop_info');
    $('<input type="hidden" name="yadelivery_info">').insertAfter('#dop_info');
    $('<input type="hidden" name="yadelivery_pvz_id">').insertAfter('#dop_info');
    $('<input type="hidden" name="yadelivery_address">').insertAfter('#dop_info');
    $('<input type="hidden" name="yadelivery_type">').insertAfter('#dop_info');
    $('<input type="hidden" name="yadelivery_city_id">').insertAfter('#dop_info');
    $('<input type="hidden" name="yadelivery_tariff">').insertAfter('#dop_info');

    $('#yandexwidgetModal').modal('toggle');
}

document.addEventListener('YaNddWidgetPointSelected', function (data) {
    $.ajax({
        url: ROOT_PATH+'/phpshop/modules/yandexdelivery/ajax/price.php',
        type: 'post',
        data: {delivery_variant_id: data.detail.id,weight: $('input[name="yandexdelivery_weight"]').val()},
        success: function (approx_price) {

            yandexWidgetOnChoose({
                id: data.detail.id,
                address: data.detail.address.full_address,
                city: data.detail.address.geoId,
                cityName: data.detail.address.locality,
                tarif: '',
                price: approx_price
            });
        }
    });
});

function yandexWidgetOnChoose(result) {
    
    //console.log(result);
    $("#makeyourchoise").val('DONE');
 
    var info = locale.cdek.pickup +': ' + result.id + ', ' +locale.cdek.pickup_address+': ' + result.address;
    result.price = Math.ceil(result.price);

    $('input[name="yadelivery_info"]').val(info);
    $('input[name="yadelivery_address"]').val(result.address);
    $('input[name="yadelivery_city_id"]').val(result.city);
    $('input[name="yadelivery_type"]').val('pvz');
    $('input[name="yadelivery_pvz_id"]').val(result.id);
    $('input[name="yadelivery_tariff"]').val(result.tarif);
    $('[id="DosSumma"]').html(result.price);
    $('[id="TotalSumma"]').html(Number(result.price) + Number($('#OrderSumma').val()) - Number($('#SkiSumma').attr('data-discount')));
    $('input[name="yadelivery_sum"]').val(result.price);
    $('input[name="city_new"]').val(result.cityName);
    $('[id="deliveryInfo"]').html(locale.cdek.pickup+': ' + result.address);
    $("#yandexwidgetModal").modal("hide");
}