<style>#catalog-menu {display:none}</style>

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
<div class="main-cart-header @hideCatalog@">

    <h2>{Ваша корзина}</h2>
        
    <a href="?cart=clean" class="btn cart-clean"> {Очистить корзину}</a> 
   

</div>

<div class="img_fix">
    @orderContentCart@
</div>



@orderContent@ 
</div>