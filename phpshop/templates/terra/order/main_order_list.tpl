<style>.left-menu {display:none}
.middle-content-block {width:100%;}
.cart-block{margin:0 auto; float:none}
</style>
<div class="col-xs-12 col-lg-9 cart-block">
    <div class="row top-button-order-row">
        <div class="pull-left">
            <h1 class="main-heading2">{Заказ} &#8470;@orderNum@</h1>
        </div>
        <div class="pull-right">
            <a href="?cart=clean" class="btn btn-main"><span class="glyphicon glyphicon-remove"></span> {Очистить корзину}</a> 
            <a href="phpshop/forms/cart/index.html" target="_blank" class="btn btn-main hidden-xs"><span class="glyphicon glyphicon-print"></span> {Печатная форма корзины}</a>
        </div>
    </div>
    <div class="row order-row-fix">
        <div class="table-responsive order-page-table-wrapper img_fix">
            @orderContentCart@
        </div>
    </div>
    <div class="row order-bottom-content">
        @orderContent@
    </div>
</div>
