function novaposhtaInit() {
    $("#novaposhtaModal").modal("toggle");
    $("#makeyourchoise").val(null);

    $('input[name="novaposhtaPvz"]').remove();
    $('input[name="novaposhtaInfo"]').remove();
    $('input[name="novaposhtaDeliveryCost"]').remove();
    $('input[name="novaposhtaCityRegion"]').remove();
    $('input[name="recipientCityRef"]').remove();
    $('input[name="recipientWarehouseRef"]').remove();

    if($('input[name="city_new').length === 0) {
        $('<input type="hidden" name="city_new">').insertAfter('#dop_info');
    }

    $('<input type="hidden" name="novaposhtaPvz">').insertAfter('#dop_info');
    $('<input type="hidden" name="novaposhtaInfo">').insertAfter('#dop_info');
    $('<input type="hidden" name="novaposhtaDeliveryCost">').insertAfter('#dop_info');
    $('<input type="hidden" name="novaposhtaCityRegion">').insertAfter('#dop_info');
    $('<input type="hidden" name="recipientCityRef">').insertAfter('#dop_info');
    $('<input type="hidden" name="recipientWarehouseRef">').insertAfter('#dop_info');
}

function novaposhtaMap() {
    NovaPoshtaInstance.redraw($("#novaposhta_city").attr('data-ref'));
}

NovaPoshta = function () {
    var self = this;

    self.map = {};
    self.pvz = [];
    self.latitude = '';
    self.longitude = '';
    self.weight = 0.1;
    self.cityInput = $("#novaposhta_city");
    self.mapInited = false;
    self.region = '';
    self.cityName = '';

    self.init = function (params) {
        self.latitude = params.latitude;
        self.longitude = params.longitude;
        self.weight = params.weight;
        self.bindEvents();
    };

    self.bindEvents = function () {
        self.cityInput.autocomplete({
            source: "/phpshop/modules/novaposhta/ajax/search_city.php",
            minLength: 2,
            autoFocus: true,
            response: function(event,ui){
                self.cityInput.removeClass('is-invalid');
                if(ui.content.length === 1) {
                    self.cityInput.val(ui.content[0].label);
                    $(".ui-autocomplete").hide();
                    self.redraw(ui.content[0].value);
                } else {
                    $(".ui-autocomplete").show();
                }
            },
            select: function( event, ui ) {
                event.preventDefault();
                self.cityInput.removeClass('is-invalid');
                self.cityInput.val(ui.item.label);
                self.redraw(ui.item.value);
            },
            change: function( event, ui ) {
                self.cityInput.removeClass('is-invalid');
                self.redraw(ui.item.value);
            }
        });

        $('#novaposhta-close').on('click', function () {
            $("#novaposhtaModal").modal("hide");
        });

        document.addEventListener('scroll', function (event) {
            var top = self.cityInput.offset().top;
            $('#ui-id-1').css({'top': (top + 35) + 'px'})

        }, true);
    };

    self.initMap = function () {
        self.map = new google.maps.Map(document.getElementById('novaposhta-map'), {
            center: {lat: Number(self.latitude), lng: Number(self.longitude)},
            zoom: 11
        });

        self.mapInited = true;
    };

    self.renderPvzOnMap = function () {
        var infowindow = new google.maps.InfoWindow();
        var marker;
        var markers = [];

        for(index in self.pvz) {
            marker = new google.maps.Marker({position: new google.maps.LatLng(Number(self.pvz[index]['latitude']), Number(self.pvz[index]['longitude']))});
            markers.push(marker);

            google.maps.event.addListener(marker, 'click', (function(marker, pvz) {
                return function() {
                    infowindow.setContent(
                        '<div id="content">'+
                            '<h4>Відділення №' + pvz['number'] + '</h4>' +
                            '<div id="bodyContent">' +
                                '<p><b>Адреса: </b>' + pvz['address'] + '</p>' +
                                '<p><b>Телефон: </b>+' + pvz['phone'] + '</p>' +
                                '<p><b>Тип: </b>' + pvz['type_title'] + '</p>' +
                            '</div>' +
                        '</div>'
                    );
                    infowindow.open(self.map, marker);

                    self.OnPvzSelected(pvz)
                }
            })(marker, self.pvz[index]));
        }
        new MarkerClusterer(self.map, markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
    };

    self.redraw = function (city) {
        $.ajax({
            url: '/phpshop/modules/novaposhta/ajax/search_city.php',
            type: 'post',
            data: { city: city},
            dataType: 'json',
            success: function(json) {
                if(!json.valid) {
                    self.cityInput.val('').addClass('is-invalid');
                } else {
                    if(self.isMapChanged(json.city.latitude, json.city.longitude)) {
                        self.latitude = json.city.latitude;
                        self.longitude = json.city.longitude;
                        self.pvz = json.pvz;
                        self.region = json.city.area_description.split(',')[1];
                        self.initMap();
                        self.renderPvzOnMap();
                        self.cityName = json.city.city
                    }
                }
            }
        });
    };

    self.isMapChanged = function (latitude, longitude) {
        if(!self.mapInited) {
            return true;
        }

        return Number(self.latitude) !== Number(latitude) || Number(self.longitude) !== Number(longitude);
    };

    self.OnPvzSelected = function (pvz) {
        $.ajax({
            url: '/phpshop/modules/novaposhta/ajax/calculate.php',
            type: 'post',
            data: {cityRef: pvz['city'], weight: self.weight},
            dataType: 'json',
            success: function(json) {
                var deliveryCost = json['price'];
                if($("#d").data('free') === 1) {
                    deliveryCost = 0;
                }

                $("#DosSumma").html(deliveryCost);
                $("#TotalSumma").html(Number(deliveryCost) + Number($('#OrderSumma').val()));
                $('#deliveryInfo').html('ПВЗ: №' + pvz['number'] + ' ' + self.cityInput.val());
                $('input[name="novaposhtaInfo"]').val('Мiсто: ' + self.cityInput.val() + ' ПВЗ: №' + pvz['number'] + ', ' + pvz['address']);
                $('input[name="novaposhtaPvz"]').val(pvz['number']);
                $('input[name="novaposhtaDeliveryCost"]').val(deliveryCost);
                $('input[name="novaposhtaCityRegion"]').val(self.region);
                $('input[name="city_new"]').val(self.cityName);
                $('input[name="recipientCityRef"]').val(pvz['city']);
                $('input[name="recipientWarehouseRef"]').val(pvz['ref']);

                $('.pvz-wrapper').html(
                    '<h5>Обрано відділення №' + pvz['number'] + '</h5>' +
                    '<div id="bodyContent">' +
                    '<p><b>Адреса: </b>' + pvz['address'] + '</p>' +
                    '<p><b>Телефон: </b>+' + pvz['phone'] + '</p>' +
                    '<p><b>Тип: </b>' + pvz['type_title'] + '</p>' +
                    '<p><b>Вартiсть доставки: </b>' + deliveryCost + ' грн.</p>' +
                    '</div>'
                );
            }
        });

        $("#makeyourchoise").val('DONE');
    }
};