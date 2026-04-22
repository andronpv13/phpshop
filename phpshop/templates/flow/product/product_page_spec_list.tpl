<h1 class="page-title d-none">{Спецпредложения}</h1>

<!-- Products & Filters Section -->
<div class="container space-top-0 space-top-md-0 space-bottom-2 space-bottom-lg-3">
    <div class="row">
        <div class="col-lg-12">

            <div class="row mb-5" >

                <!-- Select -->
                <div class="mr-2 col-lg-10">
                    <select name="s" id="filter-well-select-stat" class="js-custom-select custom-select-sm select2-hidden-accessible" size="1" style="opacity: 0;" data-hs-select2-options='{
                          "minimumResultsForSearch": "Infinity",
                          "customClass": "custom-select custom-select-sm",
                          "dropdownAutoWidth": true,
                          "width": "auto"
                            }'  aria-hidden="true">

                        <option value="?s=3">{Популярные}</option>
                        <option value="?s=2&f=2">{Дорогие}</option>
                        <option value="?s=2&f=1">{Дешевые}</option>
                    </select>
                </div>
                <!-- End Select -->

                <!-- Nav -->
                <ul class="nav nav-segment" id="filter-wel-stat">
                    <li class="list-inline-item">
                        <a class="nav-link @gridSetAactive@" href="?gridChange=1" data-toggle="tooltip" data-placement="top" title="" name="gridChange" value="1">
                            <i class="fas fa-list"></i>
                        </a>
                    </li>
                    <li class="list-inline-item ">
                        <a class="nav-link @gridSetBactive@" href="?gridChange=2" data-toggle="tooltip" data-placement="top" title="" name="gridChange" value="2">
                            <i class="fas fa-th-large"></i>
                        </a>
                    </li>
                   
                </ul>
                <!-- End Nav -->

            </div>


            <div class="template-product-list row">@productPageDis@</div>
            <div class="clearfix"></div>
            <div id="pagination-block">@productPageNav@</div>

            
        </div>

    </div>
</div>
<!-- End Products & Filters Section -->
