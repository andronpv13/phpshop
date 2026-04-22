<style>
    #catalog-menu {
        display: none
    }
	.left-content{display:none;}
	.center-block{width:100%; padding-left:0}
</style>

<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/" itemprop="item">
            <span itemprop="name">{Главная}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <li class="active">{Ваша корзина}</li>
</ol>
<div class="order">
    <div class="main-cart-header">

        <h2>{Ваша корзина}</h2>

        <a href="?cart=clean" class="btn cart-clean"> {Очистить корзину}</a>


    </div>

    <div class="main-product airSticky_stop-block">
        <div class=" d-flex align-items-start justify-content-between flex-wrap cart-wrap">
            <div class="col-8">
                <div class="stick-block">
                    @orderContent@
                </div>

            </div>
            <div class="col-4">
                <div class="stick-block">
                    <div class="img_fix">
                        @orderContentCart@
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>