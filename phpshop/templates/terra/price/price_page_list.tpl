<div class="page-header">
    <h1 class="main-heading2">Прайс-лист @priceCatName@</h1>
</div>


<ul class="nav nav-pills page-price">
    <li role="presentation"><div class="dropdown">
            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="false">
                {Выбрать каталоги}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                @searchPageCategoryDrop@
            </ul>
        </div></li>
    <li class="price-page-list" role="presentation"><a href="/shop/CID_@PageCategory@.html" id="price-form" data-uid="@PageCategory@">{Форма с описанием}</a></li>
    <li class="price-page-list" role="presentation"><a href="phpshop/forms/priceprint/print.html?catId=@PageCategory@">{Печатная форма}</a></li>
    <li class="price-page-list" role="presentation"><a href="/files/priceSave.php?catId=@PageCategory@">Excel {Форма}</a></li>
    <li class="@onlinePrice@" role="presentation"><a href="/files/onlineprice/">{Интерактивная форма}</a></li>
</ul>

<div><br></div>
<div>@productPageDis@</div>

