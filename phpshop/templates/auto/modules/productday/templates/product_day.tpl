<!-- Товара дня  -->
<a href="/shop/UID_@productDayId@.html">
<div class="card shadow-soft p-4 transition-3d-hover h-100" >
    <span class="d-block small text-danger font-weight-bold text-cap">{Товар дня}</span>
    <h4 style="z-index: 9999;">@productDayName@</h4>
    <h3 class="display-4 mb-1">@productDayPrice@ <span class="rubznak">@productValutaName@</span></h3>  
    <h4 class="text-danger"><strike>@productDayPriceN@<span class="rubznak">@productDayCurrency@</span></strike></h4>
<img class="m-4 position-absolute bottom-0 right-0 w-sm-35 max-w-27rem rounded-lg" src="@productDayPicBig@" alt="">
    <!-- Countdown -->
    <div class="w-sm-60">
        <div class="row mx-n2 mb-3">
            <div class="col-4 text-center px-2">
                <div class="border border-dark rounded p-2 mb-1">
                    <span class="js-cd-hours d-block text-dark font-size-2 font-weight-bold">@productDayHourGood@</span>
                </div>
                <span class="d-block text-dark">{Часов}</span>
            </div>
            <div class="col-4 text-center px-2">
                <div class="border border-dark rounded p-2 mb-1">
                    <span class="js-cd-minutes d-block text-dark font-size-2 font-weight-bold">@productDayMinuteGood@</span>
                </div>
                <span class="d-block text-dark">{Минут}</span>
            </div>
            <div class="col-4 text-center px-2">
                <div class="border border-dark rounded p-2 mb-1">
                    <span class="js-cd-seconds d-block text-dark font-size-2 font-weight-bold">@productDayMinuteGood@</span>
                </div>
                <span class="d-block text-dark">{Секунд}</span>
            </div>
        </div>
    </div>
    <!-- End Countdown -->
</div>
</a>
<script>

    setInterval(function () {
        var h = $(".js-cd-hours").html();
        var m = $(".js-cd-minutes").html();
        var s = parseInt($(".js-cd-seconds").html());

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
            } else{
                s--;
            }
            if (s < 10){
                s = "0" + s;
            }

            $(".js-cd-hours").html(h);
            $(".js-cd-minutes").html(m);
            $(".js-cd-seconds").html(s);
        }
    }, 1000);
</script>
<!-- / Товар дня -->