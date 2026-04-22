<style>
    @media(max-width:767px){
        .filter-panel{display:flex!important}
        .filter-panel.hide{display:none!important}
        .mobile-search.visible-xs{display:none!important}
        .mobile-search.visible-xs.visible{display:flex!important}
        .head-block {
            min-height: 101px;
        }
    }
    @media(min-width:767px){.head-block {
                                min-height: 130px;
                            }
    }
</style>

@ProductCatalogContent@

<!-- Виртуальный каталог -->
<div class="catalog-block">@vendorCatDisp@</div>
<!--/ Виртуальный каталог -->

<div class="product-filter @hideSort@" id="filter-well">
    <div class="row d-flex align-items-center">
        <div class="col-md-3 col-sm-3  col-xs-12 ">
            <div class="display d-flex @hideCatalog@">
                {Сначала}: <div class="filter-menu-wrapper">
                    <div class="btn-group filter-menu" data-toggle="buttons">

                        <label class="btn btn-sm btn-sort @unitPopularActive@">
                            <input type="radio" name="s" value="3" @unitPopularChecked@ data-url="/shop/CID_@pcatalogId@.html?s=3"> {Популярные}
                        </label>

                        <label class="btn btn-sm btn-sort @unitPriceHighActive@" >
                            <input type="radio" name="s" @unitPriceHighChecked@ value="2&f=2" data-url="/shop/CID_@pcatalogId@.html?s=2&f=2"> {Дорогие}
                        </label>
                        <label class="btn btn-sm btn-sort @unitPriceLowActive@" >
                            <input type="radio" name="s" value="2&f=1" @unitPriceLowChecked@ data-url="/shop/CID_@pcatalogId@.html?s=2&f=1"> {Дешевые}
                        </label>
                    </div>
                </div>
            </div>
        </div>  

        <div class="col-md-7">
            <div class="btn-group" role="group" aria-label="...">
                @warehouse_sort@
            </div>
        </div> 

        <div class="col-md-2 col-sm-4 hidden-xs text-right">
            <div class="display" data-toggle="buttons">
                <label class="btn btn-sm fal fa-bars btn-sort @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{Товары списком}">
                    <input type="radio" name="gridChange" value="1">
                </label>
                <label class="btn btn-sm fal fa-th btn-sort @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{Товары сеткой}">
                    <input type="radio" name="gridChange" value="2">
                </label>
            </div>
        </div>
        <div class="col-sm-2 hidden-xs hidden-md hidden-lg"> <span class="filter-btn"><span class="icons icons-filter"></span>{Фильтры}</span>
        </div>
    </div> 
    <a name="sort"></a>
    <form method="post" action="/shop/CID_@productId@@nameLat@.html" name="sort" id="sorttable" class="hide">
        <table><tr>@vendorDisp@<td>@vendorSelectDisp@</td></tr></table>
    </form>                      
</div>

<div class="template-product-list">@productPageDis@</div>
<div class="clearfix"></div>
<div id="ajaxInProgress"></div>
<div class="product-scroll-init"></div>
<div id="pagination-block">@productPageNav@</div>
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
                    setEqualHeight(".thumbnail .description");

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
            values: [new Number('@price_min@'), new Number('@price_max@')],
            slide: function (event, ui) {
                $("input[name=min]").val(ui.values[ 0 ]);
                $("input[name=max]").val(ui.values[ 1 ]);
            }
        });
    });
</script>
