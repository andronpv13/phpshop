<div class="product-page-button">
    <form action="@kvk_url@" method="post" name="kvkForm" style="display: inline;">
        <input name="shopId" value="@kvk_shop_id@" type="hidden">
        @kvk_showcase_id@
        <input name="promoCode" value="@kvk_promo@" type="hidden">
        <input name="sum" value="@kvk_sum@" type="hidden">
        <input name="orderNumber" value="@kvk_ouid@" type="hidden">
        <input name="customerEmail" value="@kvk_mail@" type="hidden">
        <input name="customerPhone" value="@kvk_tel@" type="hidden">
        @kvk_prod@
        <div class="alert alert-warning" role="alert">
            Ожидайте перехода на сайт Tinkoff Credit, либо нажмите кнопку&nbsp;&nbsp;
            <input type="submit" value="@kvk_pay@" class="btn btn-cart">
        </div>
    </form>
</div>
<script>
    setTimeout(function(){
        document.kvkForm.submit();
    }, 3000);    
</script>