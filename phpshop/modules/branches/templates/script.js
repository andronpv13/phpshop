BranchesModule = function () {
    var self = this;

    self.map = {};
    self.branches = {};

    self.init = function (params) {
        self.branches = params.branches;

        self.bindEvents();
        ymaps.ready(self.initMap);
    };

    self.bindEvents = function () {
        $('.branches-city').on('click', function() {
            self.changeCity($(this));
        });
        $('.geo-changecity').on('click', function () {
            $("#geolocationModal").modal("toggle");
        });
    };

    self.initMap = function () {
        self.map = new ymaps.Map("branches-map-container", {
            center: [62.871484, 99.658350],
            zoom: 14
        });

        self.bindPlacemarks($('.branches-cities-list li a.active').attr('data-city-id'));
    };

    self.bindPlacemarks = function (currentCityId) {

        var collection = new ymaps.GeoObjectCollection();
        for (let key in self.branches[currentCityId]) {
            var placemark = new ymaps.Placemark(
                [self.branches[currentCityId][key]['lon'], self.branches[currentCityId][key]['lat']],
                {balloonContent: self.branches[currentCityId][key]['name']},
                {iconColor: '#141ce6'}
            );

            collection.add(placemark);
        }

        self.map.geoObjects.add(collection);

        if (collection.getLength() > '1') {
            self.map.setBounds(collection.getBounds());
        } else {
            self.map.setCenter(collection.get(0).geometry.getCoordinates());
        }
    };

    self.resetMap = function() {
        self.map = {};

        $('#branches-map-container').html('');

        ymaps.ready(self.initMap);
    };

    self.changeCity = function (cityElement) {
        var currentCityId = cityElement.attr('data-city-id');

        $('.branches-cities-list .active').removeClass('active');
        cityElement.addClass('active');

        $('#currentBranchCity').html(cityElement.html());
        $('.branches-current-city').html(cityElement.html());

        $('.branch-address').each(function (index, element) {
            if($(element).attr('data-city-id') === currentCityId) {
                $(element).removeClass('hidden');
            } else {
                $(element).addClass('hidden');
            }
        });

        self.resetMap();
    };
};