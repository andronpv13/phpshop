<!-- Слайдер фильтр -->
<div class="hide left-filter" id="faset-filter">
    <div class="faset-filter-name text-right"><span class="close"><span class="fal fa-times" aria-hidden="true"></span></span></div>
    <div class="panel-body faset-filter-block-wrapper">
        <form method="get" action="/sliderfilter/">
            <div id="faset-filter-body">@vendorDisp@</div>

            <div id="price-filter-body" class="@hideCatalog@">
                <div class="h4">{Цена}</div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6" id="price-filter-val-min">
                            {от} <input type="text" class="form-control input-sm" name="min" value="@price_min@">
                        </div>
                        <div class="col-md-6 col-xs-6" id="price-filter-val-max">
                            {до} <input type="text" class="form-control input-sm" name="max" value="@price_max@">
                        </div>
                    </div>

                <div id="slider-range" class="slider-range"></div>

            </div>
            <div>
                <br>
                <input type="hidden" name="path" value="@php  echo $GLOBALS['PHPShopNav']->objNav['url']; php@">
                <button type="submit" class="btn btn-sm btn-block">{Показать}</button>
                <a href="?" class="btn btn-default btn-sm btn-block" >{Сбросить}</a>
            </div>
         </form>   
    </div>
</div>




<!--/ Слайдер фильтр -->
<style>
    #price-filter-body, #faset-filter-body{
        margin-left: 15px;
        margin-right: 15px;
    }
    #faset-filter-body .faset-filter-block-wrapper h4{
        border-bottom: 0px solid #e1e7ec;
    }
</style>