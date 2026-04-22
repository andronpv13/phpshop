<h1 class="page-title d-none">@sortName@</h1>

<div>@sortDes@</div>

<!-- Products & Filters Section -->
<div class="container space-top-0 space-top-md-0 space-bottom-2 space-bottom-lg-3">
    <div class="row">
        <div class="col-lg-12">

            <div class="row mb-5" >

                <!-- Select -->
                <div class="mr-2 col-lg-12 ">

                    <div class="filter-heading ">
                        <div class="row">

                            <div class="col-xl-4 col-lg-4 col-md-4 col-10 ">

                                <div class="shop-filter-tab">
                                    <ul class="nav nav-pillsnav-segment" role="tablist" style="align-items:center;">
                                        <li class="d-none d-md-block">{Сортировка}</li>
                                        <li>
                                            <select name="s" id="filter-well-select-stat" class="form-control" size="1">
                                                <option value="?s=3" @flowPopularActive@>{Популярные}</option>
                                                <option value="?s=2&f=2" @flowPriceHighActive@>{Дорогие}</option>
                                                <option value="?s=2&f=1" @flowPriceLowActive@>{Дешевые}</option>
                                                <option value="?s=1" @flowNameActive@>{Наименование}</option>
                                            </select>
                                        </li>

                                    </ul>
                                </div>

                            </div>
                            <div class="col-xl-8 col-lg-4 col-md-4 col-2 ">
                                <div class="shop-filter-tab">
                                    <ul class="nav nav-pillsnav-segment float-right" id="filter-well" role="tablist" style="align-items:center;">
                                        <li class="d-none d-md-block">{Вывод}</li>
                                        <li class="nav-item list-inline-item d-none d-md-block">
                                            <a class="nav-link @gridSetAactive@" data-toggle="tooltip" href="?gridChange=1" data-placement="top" data-original-title="Список"><i class="fal fa-list "></i></a>
                                        </li>
                                        <li class="nav-item d-none d-md-block" >
                                            <a class="nav-link @gridSetBactive@" data-toggle="tooltip" href="?gridChange=2" data-placement="top" data-original-title="Ячейки"><i class="fal fa-border-none"></i></a>
                                        </li>    
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
                <!-- End Select -->

            </div>

            <div class="template-product-list row">@productPageDis@</div>
            <div class="clearfix"></div>
            <div id="pagination-block">@productPageNav@</div>

        </div>

    </div>
</div>
<!-- End Products & Filters Section -->
