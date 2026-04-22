<div class="side-heading">Товар дня</div>

<div class="product-col product-day">
    <div class="image product-img-centr product-day-link">
        <a href="/shop/UID_@productDayId@.html" title="@productDayName@"><img src="@productDayPicSmall@" alt="@productDayName@" class="img-responsive img-center-sm"></a>
    </div>
    <div class="caption">
        <div>
            <h4><a href="/shop/UID_@productDayId@.html" title="@productName@">@productDayName@</a></h4>
            <!-- @productDayDescription@ -->
        </div>
        <div class="price">
            <span class="price-new">@productDayPrice@<span class="rubznak">@productValutaName@</span></span> 
            <span class="price-old">@productDayPriceN@<span class="rubznak">@productDayCurrency@</span></span>
        </div>

        <br>
        <div class="clock" data-hour="@productDayTimeGood@"></div>
    </div>
</div>
<link rel="stylesheet" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/flipclock.css">
