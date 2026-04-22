<h2 class="cart-title">{Ваша корзина}</h2>

<div class="cart-body-content">
    <div class="row">
        <div class="col-xl-8">

            <div class="product-content">
                    <div class="table-responsive">
                        <table class="table table-2">
                            <thead class="hidden-sm">
                                <tr>
                                    <th class="remove-porduct"></th>
                                    <th class="product-image"></th>
                                    <th class="product-title">{Товар}</th>
                                    <th width="100">{Цена}</th>
                                    <th class="quantity">{Количество}</th>
                                    <th class="total" width="100">{Итого}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @display_cart@
                            </tbody>
                        </table>
                    </div>
                
                <div class="d-none d-sm-block">
            <a class="link-underline small" href="phpshop/forms/cart/index.html" target="_blank"><i class="fa fa-print text-hover-primary mr-1"></i>{Печатная форма}</a>
            <a class="link-underline small float-right" href="?cart=clean"><i class="far fa-trash-alt text-hover-primary mr-1"></i>{Очистить корзину}</a>
        </div>
                
            </div>
        </div>
        <div class="col-xl-4">
            <div class="cart-widget">
                <h4 class="title">{Стоимость}</h4>
                <table class="table table-2 no-border img_fix">
                    <tbody>
                        <tr>
                            <th>{Итого}</th>
                            <td>@cart_sum_discount_off@<span class="rubznak">@currency@</span></td>
                        </tr>
                        <tr>
                            <th>{Количество}</th>
                            <td>@cart_num@ @cart_izm@</td>
                        </tr>
                        <tr>
                            <th>{Скидки и бонусы}</th>
                            <td>
                                <span id="SkiSumma" class="text-danger" data-discount="@discount_sum@">- @discount_sum@</span><span class="rubznak text-danger">@currency@</span>
                            </td>
                        </tr>
                        <tr class="@php __hide('cart_weight'); php@">
                            <th>{Вес}</th>
                            <td>
                                <span id="WeightSumma">@cart_weight@</span> {г}
                                <span id="VolumeWeightSumma" class="d-none">@cart_volume_weight@</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{Доставка}</th>
                            <td>
                                <span id="DosSumma">@delivery_price@</span><span class="rubznak">@currency@</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{К оплате с учетом скидки}</th>
                            <td><strong><span id="TotalSumma">@total@</span></b><span class="rubznak">@currency@</span></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- /. cart widget -->
        </div>
    </div>
</div>


<input type="hidden" id="OrderSumma" name="OrderSumma" value="@cart_sum_discount_off@">
<script>
    $(function () {
        $('#num').html('@cart_num@');
        $('#sum').html('@cart_sum@');
    });
</script>