<h1 class="page-title d-none">@catalogName@</h1>

<!-- Products & Filters Section -->
<div class="container space-top-0 space-top-md-0 space-bottom-1 space-bottom-lg-1">

    @ProductCatalogContent@

    <!-- Виртуальный каталог -->
    <div class="">
        <div class="row">@vendorCatDisp@</div>
    </div>
    <!--/ Виртуальный каталог -->

    <div class="row">
        <div class="col-lg-12 ">

            <div class="row mb-5 @php __hide('empty_product_list','isset'); php@">

                <!-- Select -->
                <div class="mr-2 col-lg-12">
                    <select name="s" id="filter-well-select" class="js-custom-select custom-select-sm select2-hidden-accessible" size="1" style="opacity: 0;" data-hs-select2-options='{
                            "minimumResultsForSearch": "Infinity",
                            "customClass": "custom-select custom-select-sm",
                            "dropdownAutoWidth": true,
                            "width": "auto"
                            }'  aria-hidden="true">

                        <option value="3" data-url="/shop/CID_@pcatalogId@.html?s=3" @flowPopularActive@>{Популярные}</option>
                        <option value="2&f=2" data-url="/shop/CID_@pcatalogId@.html?s=2&f=2" @flowPriceHighActive@>{Дорогие}</option>
                        <option value="2&f=1" data-url="/shop/CID_@pcatalogId@.html?s=2&f=1" @flowPriceLowActive@>{Дешевые}</option>
                        <option value="1" data-url="/shop/CID_@pcatalogId@.html?s=1" @flowNameActive@>{Наименование}</option>
                    </select>

                    <!-- Nav -->
                    <ul class="nav nav-segment float-right" id="filter-well">
                        @warehouse_sort@
                        <li class="list-inline-item d-none d-md-block">
                            <a class="nav-link filter-item @gridSetAactive@" href="#" data-toggle="tooltip" data-placement="top" title="" name="gridChange" value="1">
                                <i class="fas fa-list"></i>
                            </a>
                        </li>
                        <li class="list-inline-item d-none d-md-block">
                            <a class="nav-link filter-item @gridSetBactive@" href="#" data-toggle="tooltip" data-placement="top" title="" name="gridChange" value="2">
                                <i class="fas fa-th-large"></i>
                            </a>
                        </li>
                        <li class="list-inline-item d-block d-md-none @php __hide('vendorCatDisp','parser','d-block d-md-none'); php@">
                            <a class="nav-link" id="mobile-filter" href="#" data-toggle="modal" data-target="#MoreFiltersModal">
                                <i class="fas fa-sliders-h dropdown-item-icon"></i>
                            </a>
                        </li>

                    </ul>
                    <!-- End Nav -->
                </div>
                <!-- End Select -->
            </div>

            <!-- Filters Modal -->
            <div class="modal fade" id="MoreFiltersModal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="RealEstateMoreFiltersModalTitle">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <!-- Header -->
                        <div class="modal-header">
                            <h4 id="RealEstateMoreFiltersModalTitle" class="modal-title">{Фильтр}</h4>
                            <div class="modal-close">
                                <button type="button" class="btn btn-icon btn-xs btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                                    <svg width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="modal-body" id="mobile-filter-wrapper">
                        </div>
                        <!-- End Body -->

                        <div class="modal-footer">
                            <a  href="?" id="faset-filter-reset" class="btn btn-sm btn-white mr-2">{Сбросить}</a>
                            <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">{Применить}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filters Modal -->

            <a name="sort"></a>
            <form method="post" action="/shop/CID_@productId@@nameLat@.html" name="sort" id="sorttable" class="d-none">
                <table><tr>@vendorDisp@ <td>&nbsp;</td><td>@vendorSelectDisp@</td></tr></table>
            </form>

            <div class="template-product-list row"> 
                @productPageDis@
			</div>
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

        </div>

    </div>
</div>
<!-- End Products & Filters Section -->
