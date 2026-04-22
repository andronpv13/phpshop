<style>
    .geolocation-delivery {margin-top: 10px;}
    .geolocation-delivery-title {display: table;}
    .geolocation-delivery-title a {text-decoration:none;}
    .geolocation-delivery-title > span{padding-left:7px; padding-right:5px;}
    .geolocation-delivery-title a span{color:#575b71; border-bottom:1px dashed; -webkit-transition:all 0.15s ease 0s; -moz-transition:all 0.15s ease 0s; -o-transition:all 0.15s ease 0s; transition:all 0.15s ease 0s;}
    .geolocation-delivery-title a:hover span{color:inherit;cursor: default;}
    .geolocation-delivery-list {margin-top: 10px; color: #8184a1;}
    .geolocation-delivery-item{width:100%; display:flex; justify-content:space-between; align-items:flex-end; margin-top:5px;}
    .geolocation-delivery-dots {border-bottom:1px dotted #dee0ee; flex-grow:1;}
    .geolocation-delivery-price {white-space:nowrap;}
</style>


<div class="geolocation-delivery">
    <div class="geolocation-delivery-title">
        <i class="fa fa-map-marker"></i><span>{Доставка в}</span><a data-toggle="modal" href="#modal-delivery-city-select"><span id="delivery-city-label">@deliveryCity@</span></a>
    </div>
    <div class="geolocation-delivery-list">
        @delivery_list@
    </div>
</div>

<!-- Модальное окно для выбора города доставки -->
<div class="modal bs-example-modal-sm" id="modal-delivery-city-select" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <div class="modal-title h4">{Выберите город}</div>
            </div>
            <div class="modal-body">

                <input id="delivery-city-select" maxlength="50" class="form-control" placeholder="{Начните ввод}..." required="required" type="text" style="font-size: 16px">

            </div>

        </div>
    </div>
</div>
<!-- Модальное окно для выбора города доставки -->

<script>
    $(function () {
        $("#delivery-city-select").suggestions({
            token: "@deliveryToken@",
            type: "ADDRESS",
            bounds: "city-settlement",
            count: 5,
            geoLocation: false,
            onSelect: function (suggestion) {
                $("#city").val(suggestion.data.city);
                localStorage.setItem('deliveryCity', suggestion.data.city);
                localStorage.setItem('deliveryCityIndex', suggestion.data.postal_code);
                $('#modal-delivery-city-select').modal('hide');

                $('.geolocation-delivery').trigger('updateDeliveryCity');
            }
        });

        $('.geolocation-delivery').on('updateDeliveryCity', function () {
            const deliveryCity = localStorage.getItem('deliveryCity') ? localStorage.getItem('deliveryCity') : $('#delivery-city-label').text();

            $('#delivery-city-label').html(deliveryCity);

            $.ajax({
                url: 'phpshop/modules/deliverywidget/ajax/deliverywidget.php',
                type: 'get',
                data: {productId: @productUid@, city: deliveryCity},
                success: function (data) {
                    if (data)
                        $('.geolocation-delivery-list').html(data);
                }
            });
        });

        $('.geolocation-delivery').trigger('updateDeliveryCity');
    });
</script>