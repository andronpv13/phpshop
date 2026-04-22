<!-- Слайдер фильтр -->
<div class="hide panel panel-default" id="faset-filter">
    <div class="faset-filter-name"><span class="close"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>Фильтры</div>
    <div class="panel-body faset-filter-block-wrapper">
        <form method="get" action="/sliderfilter/">

        @vendorDisp@

        <div id="price-filter-body" class="@hideCatalog@">
            <h4>{Цена}</h4>
                <div class="row">
                    <div class="col-md-6 col-xs-6" id="price-filter-val-min">
                        {от} <input type="text" class="form-control input-sm" name="min" value="@price_min@" > 
                    </div>
                    <div class="col-md-6 col-xs-6" id="price-filter-val-max">
                        {до} <input type="text" class="form-control input-sm" name="max" value="@price_max@"> 
                    </div>
                </div>

            <div class="slider-range" id="slider-range"></div>

        </div>
        <br>
            <p>
                <input type="hidden" name="path" value="@php  echo $GLOBALS['PHPShopNav']->objNav['url']; php@">
                <button type="submit" class="btn btn-sm btn-block">{Показать}</button>
                <a href="?" class="btn btn-default btn-sm btn-block" >{Сбросить}</a>
            </p>
        
        </form>
    </div>
</div>
<!--/ Слайдер фильтр -->
<style>
#faset-filter h4 {
  border-top: 0px solid #dfe0e1;
}
</style>