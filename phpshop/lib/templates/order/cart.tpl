<table border=0 width=99% cellpadding=0 cellspacing=3>
    <tr>
        <td width=50 class="mobHideCol"></td>
        <td width="40%"><strong>{Наименование}</strong></td>
        <td width="10%" align="left"><strong>{Кол-во}</strong></td>
        <td width="10%" align="center"  class="mobHideCol"><strong>{Операции}</strong></td>
        <td width="15%" align="right"><strong>{Цена 1 шт.}</strong></td>
        <td width="15%" align="right"><strong>{Стоимость}</strong></td>
        <td align="right">&nbsp;</td>
    </tr>
    <tr>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td colspan="5">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
    </tr>
    @display_cart@
    <tr>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td colspan="5">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
    </tr>
    <tr>
        <td>
            <b>{Итого}:</b>
        </td>
        <td class="mobHideCol"></td>
        <td width="55" >
            <strong>@cart_num@</strong> ({шт.})
        </td>

        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red"></td>
        <td align="right" class="red">@cart_sum@ @currency@</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td colspan="5">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
    </tr>

    <tr style="visibility:hidden;display:none;">
        <td colspan="2">
            {Вес товаров}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><span id="WeightSumma">@cart_weight@</span> {гр.}</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td colspan="2">
            {Скидка}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red" id="SkiSummaAll"><span id="SkiSumma">@discount@</span>&nbsp;%</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td colspan="2">
            {Доставка}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><span id="DosSumma">@delivery_price@</span>&nbsp; @currency@</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td colspan="2">
            {К оплате с учетом скидки}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><b><span id="TotalSumma">@total@</span></b> @currency@</td>
        <td align="right" class="red"></td>

    </tr>
</table>
<input type="hidden" id="OrderSumma" name="OrderSumma"  value="@cart_sum@">
<script>
    if (window.document.getElementById('num')) {
        window.document.getElementById('num').innerHTML = '@cart_num@';
        window.document.getElementById('sum').innerHTML = '@cart_sum@';
    }
</script>