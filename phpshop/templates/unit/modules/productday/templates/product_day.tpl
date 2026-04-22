<div class="block hidden-xs product-day @hideSite@">
  <div class="block-heading">
    <div class="block-title">{Товар дня}</div>
  </div>
  <div class="block-body">
     <a href="/shop/UID_@productDayId@.html" class="product-day-link">
            <img class="media-object" src="@productDayPicSmall@" alt="@productDayName@">
        </a>
        <div>
            <div class="h4"><a href="/shop/UID_@productDayId@.html">@productDayName@</a></div>
            @productDayDescription@
        </div>
        <div class="product-price">@productDayPrice@<span class="rubznak">@productValutaName@</span> <span class="price-old">@productDayPriceN@ <span class="rubznak">@productDayCurrency@</span></span></div>
        <br>
        <div class="clock" data-hour="@productDayTimeGood@"></div>
  </div>
</div>

<link rel="stylesheet" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/flipclock.css">