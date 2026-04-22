<table class="table product-table">
    <tr>
        <td  colspan="2">{Ваш заказ}</td>

        <td  align="center">{Количество}</td>


        <td  align="center">{Сумма}</td>
        <td  align="center" class=""></td>
    </tr>

    @display_cart@
    <tr class="pad-10-20">
        <td>
            <b>{Итого}:</b>
        </td>

        <td width="55" >

        </td>


        <td align="right" class=""></td>
        <td colspan="2" align="right" class="red">@cart_sum_discount_off@ <span class="rubznak">@currency@</span></td>
    </tr>

    <tr class="pad-10">
        <td colspan="2">{Скидки и бонусы}:</td>

        <td></td>

        <td colspan="2" align="right" class="red" id="SkiSummaAll"><span id="SkiSumma" class="text-danger" data-discount="@discount_sum@">- @discount_sum@</span><span class="rubznak text-danger">@currency@</span></td>
    </tr> 
    <tr class="pad-20-10">
        <td colspan="3">{Доставка}: <span id="deliveryInfo"></span></td>

        <td style="display: none;"></td>

        <td colspan="3" align="right" class="red"><span id="DosSumma">@delivery_price@</span><span class="rubznak">@currency@</span></td>
    </tr>

    <tr>
        <td colspan="2">
            <b> {К оплате с учетом скидки}:</b>
        </td>

        <td class=""></td>
        <td colspan="2" align="right" class="red">
            <span id="WeightSumma" class="hidden">@cart_weight@</span>
            <span id="VolumeWeightSumma" class="hidden">@cart_volume_weight@</span>
            <b><span id="TotalSumma">@total@</span></b>&nbsp;<span class="rubznak">@currency@</span></td>
    </tr>

</table>
<input type="hidden" id="OrderSumma" name="OrderSumma"  value="@cart_sum_discount_off@">
<script>
    $(function () {
        $('#num').html('@cart_num@');
        $('#sum').html('@cart_sum@');
    });
</script>