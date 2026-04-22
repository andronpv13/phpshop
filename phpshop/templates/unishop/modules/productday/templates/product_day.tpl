<div class="col-xs-12 @hideCatalog@">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">



            <div class="product-day-wrapper">
                <h2 class="product-day-label">{Ограниченное предложение}</h2>
                <h2 class="product-day-name">
                    <span class="product-day-name-top">{Новинка}</span>
                    <a href="/shop/UID_@productDayId@.html" title="@productName@">@productDayName@</a>
                    <span class="product-day-name-bottom"></span>
                </h2>
                <div class="price">
                    <span class="price-new">@productDayPrice@<span class="rubznak">@productValutaName@</span></span> 
                    <span class="price-old">@productDayPriceN@<span class="rubznak">@productDayCurrency@</span></span>
                </div>
                <div class="product-day-img">
                    <a href="/shop/UID_@productDayId@.html" title="@productDayName@"><img src="@productDayPicSmall@" alt="@productDayName@"></a>
                </div>
                <div class="product-day-timer">
                    <div class="text-center">
                        <h4 id="timer-number-days">0</h4>
                        <small id="timer-text-days">{дней}</small>
                    </div>
                    <div class="text-center">
                        <h4 id="timer-number-hours">@productDayHourGood@</h4>
                        <small id="timer-text-hours">{часов}</small>
                    </div>
                    <div class="text-center">
                        <h4 id="timer-number-minutes">@productDayMinuteGood@</h4>
                        <small id="timer-text-minutes">{минут}</small>
                    </div>
                    <div class="text-center">
                        <h4 id="timer-number-seconds">@productDaySecondGood@</h4>
                        <small id="timer-text-seconds">{секунд}</small>
                    </div>
                </div>
                <div class="product-day-button">
                    <a class="btn btn-main" href="/shop/UID_@productDayId@.html" title="@productDayName@">{Перейти к товару}</a>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 text-center">
            <div class="sticker-wrapper">
                <a href="/shop/UID_@productDayId@.html" title="@productDayName@"><img src="@productDayPicBig@" title="@productDayName@" alt="@productDayName@"></a>
            </div>
        </div>
    </div>
</div>
<script >

    $().ready(function () {
        setInterval(function () {
            var h = $("#timer-number-hours").html();
            var m = $("#timer-number-minutes").html();
            var s = parseInt($("#timer-number-seconds").html());

            if (m != "") {
                if (s == 0) {
                    if (m == 0) {
                        if (h == 0) {
                            return;
                        }
                        h--;
                        m = 60;
                        if (h < 10)
                            h = "0" + h;
                    }
                    m--;
                    if (m < 10)
                        m = "0" + m;
                    s = 59;
                } else
                    s--;
                if (s < 10)
                    s = "0" + s;

                $("#timer-number-hours").html(h);
                $("#timer-number-minutes").html(m);
                $("#timer-number-seconds").html(s);
            }
        }, 1000);
    });

</script>