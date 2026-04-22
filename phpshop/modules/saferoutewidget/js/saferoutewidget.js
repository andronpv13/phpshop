function saferoutewidgetStart() {
  $('input[name="saferouteReq"]').remove();

  if(!$('input[name="saferouteSum"]').length)
    $('<input type="hidden" name="saferouteSum" id="saferouteSum">').insertAfter('#d');
  if(!$('input[name="saferouteDop"]').length)
    $('<input type="hidden" name="saferouteDop">').insertAfter('#dop_info');
  if(!$('input[name="saferouteData"]').length)
    $('<input type="hidden" name="saferouteData">').insertAfter('#dop_info');
  $('<input type="hidden" name="saferouteReq" class="req form-control">').insertAfter('#dop_info');

  var userData = $.parseJSON($("input:hidden.userDataJson").val());

  var fio = userData['fio'];
  var email = userData['email'];
  var phone = '';
  if(userData['address'].hasOwnProperty('phone')) {
    phone = userData['address']['phone'];
  }
  var city = '';
  if(userData['address'].hasOwnProperty('city')) {
    city = userData['address']['city'];
  }
  var street = '';
  if(userData['address'].hasOwnProperty('street')) {
    street = userData['address']['street'];
  }
  var house = '';
  if(userData['address'].hasOwnProperty('house')) {
    house = userData['address']['house'];
  }
  var flat = '';
  if(userData['address'].hasOwnProperty('flat')) {
    flat = userData['address']['flat'];
  }

  if($('input[name="mail"]').length > 0 && $('input[name="mail"]').val().length > 0) {
    email = $('input[name="mail"]').val();
  }
  if($('input[name="name_new"]').length > 0 && $('input[name="name_new"]').val().length > 0) {
    fio = $('input[name="name_new"]').val();
  }

  var widget = new SafeRouteCartWidget('saferoute-widget', {
    apiScript: '/phpshop/modules/saferoutewidget/api/saferoute-widget-api.php',
    products: $.parseJSON($("input:hidden.cartListJson").val()),
    weight: $('#ddweight').val(),
    userFullName: fio,
    userPhone: phone,
    userEmail: email,
    regionName: city,
    userAddressStreet: street,
    userAddressBuilding: house,
    userAddressApartment: flat,
    mod: 'bitrix',
  });

  widget.on('error', function (e) {
    console.error(e);
  });

  widget.on('done', function (response) {
    $('<input type="hidden" name="saferouteToken" value="' + response.id + '">').insertAfter('#d');
    $('input[name="saferouteReq"]').val($('input[name="saferouteDop"]').val());
    $('#saferoute-close').text(locale.close).addClass('btn-success');
  });

  // https://saferoute.atlassian.net/wiki/spaces/widgets/pages/33039
  widget.on('change', function (data) {
    if(data.delivery) {
      var deliveryCost = data.delivery.totalPrice;
      if($("#d").data('free') === 1) {
        deliveryCost = 0;
      }
      var total = deliveryCost + Number($('#OrderSumma').val());

      $('input[name="saferouteDop"]').val(data._meta.commonDeliveryData);
      $('#deliveryInfo').html(data._meta.commonDeliveryData);
      $('#DosSumma').html(deliveryCost);
      $('#TotalSumma').html(total.toFixed(2));
      $('#saferouteSum').val(deliveryCost);
    }

    $('input[name="name_new"]').val(data.contacts.fullName);
    $('input[name="fio_new"]').val(data.contacts.fullName);

    if(data.contacts.phone) $('input[name="tel_new"]').val(data.contacts.phone.substring(1));
    $('input[name="flat_new"]').val(data.contacts.address.apartment);
    $('input[name="house_new"]').val(data.contacts.address.building);
    $('input[name="street_new"]').val(data.contacts.address.street);
    $('input[name="index_new"]').val(data.contacts.address.zipCode);

    if(data.city) $('input[name="city_new"]').val(data.city.name);

    $('input[name="saferouteData"]').val(JSON.stringify(data, null, 0));

    if (window.saferoutewidgetHook) window.saferoutewidgetHook(data);
  });

  $('#saferoutewidgetModal').modal('toggle');
}
