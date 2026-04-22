<!-- Слайдер фильтр -->
<div class="d-none space-1" id="faset-filter">

    <div class="panel-body faset-filter-block-wrapper">
        <form method="get" action="/sliderfilter/">

        <div id="">@vendorDisp@</div>

        <div id="price-filter-body" class="border-bottom pb-4 mb-4">
            <div class="h4">{Цена}</div>
                <div class="row">
                    <div class="col-md-6 col-xs-6" id="price-filter-val-min">
                        <input type="text" class="form-control form-control-sm" name="min" value="@price_min@">
                    </div>
                    <div class="col-md-6 col-xs-6" id="price-filter-val-max">
                        <input type="text" class="form-control form-control-sm" name="max" value="@price_max@">
                    </div>
                </div>
            <br>

            <div id="slider-range" class="slider-range"></div>

        </div>
        <input type="hidden" name="path" value="@php  echo $GLOBALS['PHPShopNav']->objNav['url']; php@">
        <button type="submit" id="" class="generic-btn black-hover-btn w-10">{Показать}</button>
        <a href="?" id="faset-filter-reset" class="generic-btn black-hover-btn w-10">{Сбросить фильтр}</a>
        
        </form>
    </div>
</div>
<!--/ Слайдер фильтр -->