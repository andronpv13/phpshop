<form action="@kvk_url@" method="post" style="display: inline;">
    <input name="shopId" value="@kvk_shop_id@" type="hidden">
    @kvk_showcase_id@
    <input name="promoCode" value="@kvk_promo@" type="hidden">
    <input name="sum" value="@kvk_sum@" type="hidden">
    <input name="itemVendorCode_0" value="@kvk_uid@" type="hidden">
    <input name="itemName_0" value="@kvk_name@" type="hidden">
    <input name="itemQuantity_0" value="1" type="hidden">
    <input name="itemPrice_0" value="@kvk_sum@" type="hidden">
    <input type="submit" value="@kvk_pay@" class="btn btn-cart btn-kvk" style="margin-bottom:15px;">
</form>
<style>
    .btn-kvk {margin-bottom: 15px!important;background: #fff!important;color: red!important;border:solid 1px red!important;-webkit-transition: 0.5s;-moz-transition: 0.5s;transition: 0.5s;}
    .btn-kvk:hover {color: orange!important;background: rgba(255, 165, 0, 0.2)!important;border: solid 1px orange!important;}
</style>