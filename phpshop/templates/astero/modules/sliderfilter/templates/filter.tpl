<!-- Слайдер фильтр -->
<div class="hide" id="faset-filter">
    <h3 class="side-heading filter-title">{Фильтр товаров }<a href="?" id="faset-filter-reset" data-toggle="tooltip" data-placement="top" title="{Сбросить фильтр}"><span class="glyphicon glyphicon-remove"></span></a></h3>                    
    <div class="list-group filter-body-fix">
        <form method="get" action="/sliderfilter/">
            <div id="faset-filter-body">@vendorDisp@</div>
            <div id="price-filter-body">
                <h4>{Цена}</h4>

                <div class="row" style="padding-left: 15px;padding-right: 15px;">
                    <div class="col-md-6" id="price-filter-val-min">
                        <span>{от}</span>
                        <input type="text" class="form-control input-sm" name="min" value="@price_min@" > 
                    </div>
                    <div class="col-md-6" id="price-filter-val-max">
                        <span>{до}</span>
                        <input type="text" class="form-control input-sm" name="max" value="@price_max@"> 
                    </div>
                </div>
                <p></p>
                <div class="slider-range" id="slider-range"></div>

                <br>
                <p>
                    <input type="hidden" name="path" value="@php  echo $GLOBALS['PHPShopNav']->objNav['url']; php@">
                    <button type="submit" class="btn btn-sm btn-block">{Показать}</button>
                    <a href="?" class="btn btn-default btn-sm btn-block" >{Сбросить}</a>
                </p>
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
</style>