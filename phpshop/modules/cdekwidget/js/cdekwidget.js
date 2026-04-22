function cdekwidgetWidget() {
    var path = '../phpshop/';
    if (Number(PHPShopCDEKOptions.admin) === 1) {
        path = '../';
    }

    var country = 'all';
    if (Number(PHPShopCDEKOptions.russiaOnly) === 1) {
        country = locale.cdek.country;
    }

    new ISDEKWidjet({
        defaultCity: PHPShopCDEKOptions.defaultCity,
        country: country,
        cityFrom: PHPShopCDEKOptions.cityFrom,
        link: 'forpvz',
        popup: true,
        path: path + 'modules/cdekwidget/templates/scripts/',
        servicepath: path + 'modules/cdekwidget/api/PHPShopCdekService.php',
        templatepath: path + 'modules/cdekwidget/templates/scripts/template.php',
        goods: PHPShopCDEKOptions.products,
        onChoose: cdekWidgetOnChoosePvz,
        /*onChooseProfile: cdekwidgetOnChooseProfile,*/
        onChooseAddress: cdekwidgetonChooseAddress,
        detailAddress: true,
        apikey: PHPShopCDEKOptions.ymapApiKey
    });
}

// Курьерская доставка выбор точного адреса доставки. Корзина
function cdekwidgetonChooseAddress(result) {
    var info = locale.cdek.express_delivery + ': ' + result.address;
    
    $('input[name="cdek_type"]').val('courier');
    $('input[name="cdekInfo"]').val(info);
    $('#deliveryInfo').html(locale.cdek.express_delivery + ': ' + result.address);
    
    cdekwidgetOnChoose(result);

    $('input[name="cdek_type"]').val('courier');
    $('input[name="cdekInfo"]').val(info);
    $('#deliveryInfo').html(locale.cdek.express_delivery + ': ' + result.address);

}

// Доставка до ПВЗ. Корзина
function cdekWidgetOnChoosePvz(result) {
    var info = locale.cdek.pickup_code + ': ' + result.id + ', ' + locale.cdek.city + ' ' + result.cityName + ', ' + locale.cdek.pickup_address + ' ' + result.PVZ.Address + ', ' + locale.cdek.pickup_phone + ' ' + result.PVZ.Phone;
    
    $('input[name="cdekInfo"]').val(info);
    $('input[name="cdek_pvz_id"]').val(result.id);
    $('input[name="cdek_type"]').val('pvz');
    $('#deliveryInfo').html(locale.cdek.pickup + ': ' + result.PVZ.Address);
    
    cdekwidgetOnChoose(result);

    $('input[name="cdekInfo"]').val(info);
    $('input[name="cdek_pvz_id"]').val(result.id);
    $('input[name="cdek_type"]').val('pvz');
    $('#deliveryInfo').html(locale.cdek.pickup + ': ' + result.PVZ.Address);

}

// Курьерская доставка выбор города. Корзина
function cdekwidgetOnChooseProfile(result) {
    var info = locale.cdek.express_delivery + ': ' + locale.cdek.city + ' ' + result.cityName;
    
    cdekwidgetOnChoose(result);

    $('input[name="cdek_type"]').val('courier');
    $('input[name="cdekInfo"]').val(info);
    $('#deliveryInfo').html(info);

}

// Общие данные для обоих доставок. Корзина
function cdekwidgetOnChoose(result) {
    $("#cdekwidgetModal").modal("hide");

    if (Number(PHPShopCDEKOptions.admin) === 1) {
        cdekAdminWidgetOnChoose(result);
    } else {
        $("#makeyourchoise").val('DONE');
        $('input[name="city_new"]').val(result.cityName);
        $('#cdekSum').val(Number(result.price));
        $("#DosSumma").html(Number(result.price));

        // Учет промокода
        if ($("#promocode").parent('.form-group, .input-group').hasClass("has-success"))
            $("#TotalSumma").html(Number(result.price) + Number($('#OrderSumma').val()));
        else
            $("#TotalSumma").html(Number(result.price) + Number($('#OrderSumma').val()) - Number($('#SkiSumma').attr('data-discount')));

        //console.log(result);
        //console.log(Number(result.price) +'-'+Number($('#OrderSumma').val())+'+'+Number($('#SkiSumma').attr('data-discount')));
    }

    $('input[name="cdek_city_id"]').val(result.city);
    $('input[name="cdek_tariff"]').val(result.tarif);
}

function cdekwidgetStart() {
    $('input[name="cdekSum"]').remove();
    $('input[name="cdekInfo"]').remove();
    $('input[name="cdek_pvz_id"]').remove();
    $('input[name="cdek_city_id"]').remove();
    $('input[name="cdek_type"]').remove();
    $('input[name="cdek_tariff"]').remove();

    $('<input type="hidden" name="cdekSum" id="cdekSum">').insertAfter('#dop_info');
    $('<input type="hidden" name="cdekInfo">').insertAfter('#dop_info');
    $('<input type="hidden" name="cdek_pvz_id">').insertAfter('#dop_info');
    $('<input type="hidden" name="cdek_city_id">').insertAfter('#dop_info');
    $('<input type="hidden" name="cdek_type">').insertAfter('#dop_info');
    $('<input type="hidden" name="cdek_tariff">').insertAfter('#dop_info');

    $("#makeyourchoise").val(null);

    var isIE = /*@cc_on!@*/false || !!document.documentMode;
    if (isIE) {
        $('#forpvz').html('<div class="alert alert-danger" role="alert">Пожалуйста, оформите заказ с помощью другого браузера. Выбор пункта выдачи заказов СДЭК может работать некорректно в браузере Internet Explorer.</div>');
    } else {
        cdekwidgetWidget();
    }

    $("#cdekwidgetModal").modal("toggle");
}