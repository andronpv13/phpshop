
<style type="text/css">


    .sidebar-right .side-heading, .sidebar-right .sidebar-nav, .sidebar-right #faset-filter, .sidebar-right .panel.panel-default {
        display: none;
    }
    .order-page-sidebar-user-block,
    .sidebar-right .user-title.side-heading {
        display: block;
    }
    .main-container{padding-top: 10px;margin-top: 0;}
    .main-container > .row {
        margin-left: 0;
        margin-right: 0;
        padding-top: 15px;
        background-color: #fff;margin-top: 0px;
    }
</style>
<div class="col-xs-12 col-lg-12">
    <div class="row top-button-order-row">
        <div class="pull-left">
            <h1 class="page-title hide">{Заказ} &#8470;@orderNum@</h1>
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
