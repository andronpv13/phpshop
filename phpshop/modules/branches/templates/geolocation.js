GeolocationModule = function () {
    var self = this;

    self.searchInput = $('input[name="geolocation-search"]');
    self.cityElement = $('.geo-changecity');

    self.init = function () {
        self.bindEvents();
    };

    self.bindEvents = function () {
        self.cityElement.on('click', function () {
            self.loadRegions();
        });
        $('.city-bubble a').on('click', function () {
            self.select($(this).html());
        });
        $('body').on('click', '.geolocation-region', function () {
            self.changeRegion($(this).attr('data-region-id'));
        });
        $('body').on('click', '.geolocation-city', function () {
            self.select($(this).html());
        });

        self.searchInput.autocomplete({
            source: "/phpshop/modules/branches/ajax/geolocation.php",
            minLength: 2,
            autoFocus: true,
            open: function(event,ui){
                $(".ui-autocomplete").show();
            },
            select: function( event, ui ) {
                self.select(ui.item.value)
            },
            change: function( event, ui ) {
                self.select(self.searchInput.val())
            }
        });

        document.addEventListener('scroll', function (event) {
            var top = self.searchInput.offset().top;
            $('#ui-id-1').css({'top': (top + 35) + 'px'})

        }, true);
    };

    self.loadRegions = function() {
        $.ajax({
            url: '/phpshop/modules/branches/ajax/geolocation.php',
            type: 'post',
            data: { loadRegions: 1 },
            dataType: 'json',
            success: function (result) {
                if(result['success']) {
                    $('.geolocation-regions').html(result['regions']);
                    $('.geolocation-cities').html(result['cities']);
                    $("#geolocationModal").modal("toggle");
                } else {
                    console.log(result['error']);
                }
            }
        });
    }

    self.select = function(city) {
        $.ajax({
            url: '/phpshop/modules/branches/ajax/geolocation.php',
            type: 'post',
            data: { city: city },
            dataType: 'json',
            success: function (result) {
                if(result['success']) {
                    self.cityElement.html(city);
                    $("#geolocationModal").modal("hide");
                } else {
                    console.log(result['error']);
                }
            }
        });
    };

    self.changeRegion = function(regionId) {
        $('.geolocation-regions li').each(function (index, elem) {
            if($(elem).hasClass('geolocation-active')) {
                $(elem).removeClass('geolocation-active');
            }
        });

        $('a[data-region-id="' + regionId + '"]').parent().addClass('geolocation-active');

        $.ajax({
            url: '/phpshop/modules/branches/ajax/geolocation.php',
            type: 'post',
            data: { changedRegionId: regionId},
            dataType: 'json',
            success: function (result) {
                if(result['success']) {
                    $('.geolocation-cities').html(result['cities']);
                } else {
                    console.log(result['error']);
                }
            }
        });
    }
};