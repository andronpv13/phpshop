<ol class="breadcrumb hidden-xs" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/" itemprop="item">
            <span itemprop="name">{Главная}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <li class="active">{Прайс-лист}</li>
</ol>

<div class="page-header">
    <h2>{Прайс-лист} @priceCatName@</h2>
</div>


<ul class="nav nav-pills">
    <li role="presentation"><div class="dropdown">
            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                {Выбрать каталоги}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                @searchPageCategoryDrop@
            </ul>
        </div></li>
    <li role="presentation"><a href="/shop/CID_@PageCategory@.html" id="price-form" data-uid="@PageCategory@">{Форма с описанием}</a></li>
    <li role="presentation"><a href="phpshop/forms/priceprint/print.html?catId=@PageCategory@">{Печатная форма}</a></li>
    <li role="presentation"><a href="/files/priceSave.php?catId=@PageCategory@">Excel {Форма}</a></li>
    <li role="presentation" class="@onlinePrice@"><a href="/files/onlineprice/">{Интерактивная форма}</a></li>
</ul>

<p>@productPageDis@</p>

