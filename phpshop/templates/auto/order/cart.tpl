<!-- Cart Section -->
<div class="row">
    <div class="col-lg-8 mb-7 mb-lg-0">

        <!-- Title -->
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-7">
            <h1 class="h3 mb-0">{Ваша корзина}</h1>
            <span><a class="font-weight-bold d-none d-sm-block" href="javascript:history.back();">
                    <i class="fas fa-angle-left fa-xs mr-1"></i>
                    {Продолжить покупку}
                </a></span>
        </div>
        <!-- End Title -->

        @display_cart@

        <div class="d-none d-sm-block">
            <a class="link-underline small" href="phpshop/forms/cart/index.html" target="_blank"><i class="fa fa-print text-hover-primary mr-1"></i>{Печатная форма}</a>
            <a class="link-underline small float-right" href="?cart=clean"><i class="far fa-trash-alt text-hover-primary mr-1"></i>{Очистить корзину}</a>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="pl-lg-4">
            <!-- Order Summary -->
            <div class="card shadow-soft p-4 mb-4 img_fix">
                <!-- Title -->
                <div class="border-bottom pb-4 mb-4">
                    <h2 class="h3 mb-0">{Стоимость}</h2>
                </div>
                <!-- End Title -->

                <div class="border-bottom pb-4 mb-4">

                    <div class="media align-items-center mb-3">
                        <span class="d-block font-size-1 mr-3">{Итого} (@cart_num@ @cart_izm@)</span>
                        <div class="media-body text-right">
                            <span class="text-dark font-weight-bold">@cart_sum_discount_off@<span class="rubznak">@currency@</span></span>
                        </div>
                    </div>
                    <div class="media align-items-center mb-3">
                        <span class="d-block font-size-1 mr-3">{Скидки и бонусы}</span>
                        <div class="media-body text-right">
                            <span class="text-dark font-weight-bold"><span id="SkiSumma" class="text-danger" data-discount="@discount_sum@">- @discount_sum@</span><span class="rubznak text-danger">@currency@</span></span>
                        </div>
                    </div>

                    <div class="media align-items-center mb-3 @php __hide('cart_weight'); php@">
                        <span class="d-block font-size-1 mr-3">{Вес}</span>
                        <div class="media-body text-right">
                            <span class="text-dark font-weight-bold"><span id="WeightSumma">@cart_weight@</span> {г}</span>
                            <span id="VolumeWeightSumma" class="d-none">@cart_volume_weight@</span>
                        </div>
                    </div>

                    <div class="media align-items-center mb-3">
                        <span class="d-block font-size-1 mr-3">{Доставка}</span>
                        <div class="media-body text-right">
                            <span class="text-dark font-weight-bold"><span id="DosSumma">@delivery_price@</span><span class="rubznak">@currency@</span> </span>
                        </div>

                    </div>
                    <span class="small" id="deliveryInfo"></span>

                </div>

                <div class="media align-items-center mb-3">
                    <span class="d-block font-size-1 mr-3 font-weight-bold">{К оплате с учетом скидки}</span>
                    <div class="media-body text-right">
                        <span class="text-dark font-weight-bold"><b><span id="TotalSumma">@total@</span></b><span class="rubznak">@currency@</span></span>
                    </div>
                </div>


            </div>
            <!-- End Order Summary -->

        </div>
    </div>

</div>
<!-- End Cart Section -->

<input type="hidden" id="OrderSumma" name="OrderSumma" value="@cart_sum_discount_off@">
<script>
    $(function () {
        $('#num').html('@cart_num@');
        $('#sum').html('@cart_sum@');
    });
</script>