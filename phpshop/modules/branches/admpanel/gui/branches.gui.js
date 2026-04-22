$(document).ready(function () {
    var BranchesModuleAdminInstance = new BranchesModuleAdmin();
    BranchesModuleAdminInstance.init();
});

BranchesModuleAdmin = function () {
    var self = this;
    self.map = {};
    self.placemark = false;
    self.lonElement = $('input[name="lon_new"]');
    self.latElement = $('input[name="lat_new"]');

    self.init = function () {
        ymaps.ready(self.buildMap);
    };

    self.buildMap = function () {

        self.map = new ymaps.Map("map-container", {
            center: [55.76, 37.64],
            zoom: 15
        });

        self.placemark = new ymaps.Placemark();
        self.map.geoObjects.add(self.placemark);

        if(self.lonElement.val() !== '' && self.latElement.val() !== '') {
            self.placemark.geometry.setCoordinates([self.lonElement.val(), self.latElement.val()]);

            self.map.setCenter([self.lonElement.val(), self.latElement.val()]);
        }

        var searchControl = self.map.controls.get('searchControl');
        searchControl.options.set('provider', 'yandex#map');
        searchControl.options.set('noPlacemark', true);

        searchControl.events.add('resultselect', function (e) {
            searchControl.getResult(e.get('index')).then(function (value) {
                self.lonElement.val(value.geometry.getCoordinates()[0]);
                self.latElement.val(value.geometry.getCoordinates()[1]);

                self.placemark.geometry.setCoordinates(value.geometry.getCoordinates());
            });
        }, this);

        self.map.events.add('click', function (e) {
            var coords = e.get('coords');

            self.lonElement.val(coords[0]);
            self.latElement.val(coords[1]);

            self.placemark.geometry.setCoordinates(e.get('coords'));
        });

    };
};