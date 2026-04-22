
@ProductCatalogContent@
<!-- Виртуальный каталог -->
<div class="catalog-block">@vendorCatDisp@</div>
<!--/ Виртуальный каталог -->
<!-- Product Filter Starts -->
<div class="product-filter" id="filter-well">
    <div class="row">
        <div class="col-md-2 hidden-xs">
            <div class="display" data-toggle="buttons">
                <label class="btn btn-sm glyphicon glyphicon-th-list btn-sort @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{Товары списком}">
                    <input type="radio" name="gridChange" value="1">
                </label>
                <label class="btn btn-sm glyphicon glyphicon-th btn-sort @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{Товары сеткой}">
                    <input type="radio" name="gridChange" value="2">
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="btn-group" role="group" aria-label="...">
                @warehouse_sort@
            </div>
        </div> 
        <div class="col-md-4 filter-well-right-block col-xs-12">
            <div class="display">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-signal @sSetCactive@" data-toggle="tooltip" data-placement="top" title="{По умолчанию}">
                        <input type="radio" name="s" value="3">
                    </label>
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-sort-by-alphabet @sSetAactive@" data-toggle="tooltip" data-placement="top" title="{Наименование}">
                        <input type="radio" name="s" value="1">
                    </label>
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-sort-by-order @sSetBactive@" data-toggle="tooltip" data-placement="top" title="{Цена}">
                        <input type="radio" name="s" value="2">
                    </label>
                </div>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-sort glyphicon glyphicon-sort-by-attributes @fSetAactive@" data-toggle="tooltip" data-placement="top" title="{По возрастанию}">
                        <input type="radio" name="f" value="1">
                    </label>
                    <label class="btn btn-xs btn-sort glyphicon glyphicon-sort-by-attributes-alt @fSetBactive@" data-toggle="tooltip" data-placement="top" title="{По убыванию}">
                        <input type="radio" name="f" value="2">
                    </label>
                </div>
            </div>
        </div>
    </div> 
    <a name="sort"></a>
    <form method="post" action="/shop/CID_@productId@@nameLat@.html" name="sort" id="sorttable" class="hide">
        <table><tr>@vendorDisp@<td>@vendorSelectDisp@</td></tr></table>
    </form>                      
</div>
<!-- Product Filter Ends -->


<div class="big-filter-wrapper">
    <!-- Фасетный фильтр -->
    <div class="hide" id="faset-filter">
        <div class="filter-inner">
            <div id="faset-filter-body">{Загрузка}...</div>
            <div id="price-filter-body">
                <div class="faset-filter-block-wrapper @hideCatalog@">
                    <h4>{Цена}</h4>
                    <div>
                        <form method="get" id="price-filter-form">
                            <div class="row">
                                <div class="col-xs-6" id="price-filter-val-min">
                                    <span>{от}</span>
                                    <input type="text" class="form-control-price form-control input-sm" name="min" value="@price_min@" > 
                                </div>
                                <div class="col-xs-6" id="price-filter-val-max">
                                    <span>{до}</span>
                                    <input type="text" class="form-control-price form-control input-sm" name="max" value="@price_max@"> 
                                </div>
                            </div>
                            <div id="slider-range" class="hide"></div>
                        </form>
                    </div>
                </div>
            </div>
            <a href="?" id="faset-filter-reset" data-toggle="tooltip" data-placement="top" title="{Сбросить фильтр}"><span>{Сбросить фильтр}</span>x</a>
        </div>
    </div>
    <!--/ Фасетный фильтр -->
</div>



<div class="row template-product-list products-list">@productPageDis@</div>

<div id="ajaxInProgress"></div>
<div class="product-scroll-init"></div>
<div id="pagination-block">@productPageNav@</div>
<ul class="brand-list brand-list-catalog">@brandsList@</ul>

<script type="text/javascript">

    var max_page = new Number('@max_page@');
    var current = '@productPageThis@';
    var catalogFirstPage = '@catalogFirstPage@';
    if (current !== 'ALL')
        var count = new Number('@productPageThis@');
    else
        var count = max_page;

    // Функция подгрузки товаров
    function scroll_loader() {

        if (count < max_page) {

            // Анимация загрузки
            $('#ajaxInProgress').addClass('progress-scroll');

            var next_page = new Number(count) + 1;
            url = "/shop/CID_@pcatalogId@@page_prefix@" + next_page + "@seomod@.html@page_filters@?@page_postfix@" + window.location.hash.split('#').join('').split(']').join('][]');

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    ajax: true,
                    json: true
                },
                success: function (data)
                {
                    // Анимация загрузки
                    $('#ajaxInProgress').removeClass('progress-scroll');

                    $("#pagination-block").html(data['pagination']);

                    // Добавляем товары в общему списку
                    $(".template-product-list").append(data['products']);

                    // Выравнивание ячеек товара
                    setEqualHeight($(".products-list .description"));

                    // lazyLoad
                    setTimeout(function () {
                        $(window).lazyLoadXT();
                    }, 50);

                    count = next_page;

                    Waypoint.refreshAll();
                },
                error: function () {
                    $('#ajaxInProgress').removeClass('progress-scroll');
                }
            });
        }
    }

    // Блокировка вывода штатной пагинации [1-10]
    if (AJAX_SCROLL_HIDE_PAGINATOR) {
        $(".pagination").hide();
    }

    var price_min = new Number('@price_min@');
    var price_max = new Number('@price_max@');

    $(document).ready(function () {

        var inview = new Waypoint.Inview({
            element: $('.product-scroll-init'),
            enter: function (direction) {
                if (AJAX_SCROLL)
                    scroll_loader();
            }
        });


        $("#slider-range").slider({
            range: true,
            step: 5,
            min: new Number('@price_min@'),
            max: new Number('@price_max@'),
            values: [price_min, price_max],
            slide: function (event, ui) {
                $("input[name=min]").val(ui.values[ 0 ]);
                $("input[name=max]").val(ui.values[ 1 ]);
            }
        });
    });
</script>
