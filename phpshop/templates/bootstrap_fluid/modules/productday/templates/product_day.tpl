<div class="@hideCatalog@">
    <div class="panel panel-default hidden-xs visible-lg visible-md product-day">
        <div class="panel-heading">{Товар дня}</div>
        <div class="panel-body">

            <a href="/shop/UID_@productDayId@.html" class="product-day-link">
                <img class="media-object" src="@productDayPicSmall@" alt="@productDayName@">
            </a>
            <div >
                <h4 class="media-heading"><a href="/shop/UID_@productDayId@.html">@productDayName@</a></h4>
                @productDayDescription@
            </div>
            <h3 class="product-price">@productDayPrice@<span class="rubznak">@productValutaName@</span> <span class="price-old">@productDayPriceN@ <span class="rubznak">@productDayCurrency@</span></span></h3>
            <br>
            <div class="clock" data-hour="@productDayTimeGood@"></div>
        </div>
    </div>
    <link rel="stylesheet" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/flipclock.css">
</div>