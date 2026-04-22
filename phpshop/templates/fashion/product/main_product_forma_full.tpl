<!-- Hero Section -->
<div class="container space-top-1 space-top-sm-1" itemscope itemtype="http://schema.org/Product" style="max-width: 1200px;">
    <meta itemprop="image" content="@productImg@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="container p-0">
        <h1 itemprop="name" class="h2 page-title d-none">@productName@</h1>                          
    </div>
    <div class="row">
        <div class="col-lg-7 col-md-7 mb-7 mb-lg-0">
            <div class="pr-lg-4">
                <div class="position-relative">

                    <!-- Main Slider -->
                    <div id="heroSlider" class="js-slick-carousel slick shadow-soft"
                         data-hs-slick-carousel-options='{
                         "prevArrow": "<span class=\"fas fa-arrow-left slick-arrow slick-arrow-primary-white slick-arrow-left slick-arrow-centered-y shadow-soft rounded-circle ml-n3 ml-sm-2 ml-xl-4\"></span>",
                         "nextArrow": "<span class=\"fas fa-arrow-right slick-arrow slick-arrow-primary-white slick-arrow-right slick-arrow-centered-y shadow-soft rounded-circle mr-n3 mr-sm-2 mr-xl-4\"></span>",
                         "fade": true,
                         "infinite": true,
                         "autoplay": true,
                         "autoplaySpeed": 7000,
                         "asNavFor": "#heroSliderNav"
                         }'>
                        @productHeroSlider@
                    </div>
                    <!-- End Main Slider -->

                    <!-- Slider Nav -->
                    <div class="position-absolute bottom-0 right-0 left-0 px-4 py-3">
                        <div id="heroSliderNav" class="js-slick-carousel slick slick-gutters-1 slick-transform-off mx-auto"
                             data-hs-slick-carousel-options='{
                             "infinite": true,
                             "autoplaySpeed": 7000,
                             "slidesToShow": @productHeroCount@,
                             "isThumbs": true,
                             "isThumbsProgressCircle": true,
                             "thumbsProgressOptions": {
                             "color": "#377DFF",
                             "width": 8
                             },
                             "thumbsProgressContainer": ".js-slick-thumb-progress",
                             "asNavFor": "#heroSlider"
                             }'>
                            @productHeroSliderNav@
                        </div>
                    </div>
                    <!-- End Slider Nav -->
                </div><div class="m-3 p-3 mb-2">@promotionInfo@</div>

            </div>
        </div>

        <!-- Product Description -->
        <div class="col-lg-5 col-md-5">
            <div class="mb-5 mb-0">
                @productArt@
            </div>    
            <!-- Rating -->
            <div class="d-flex align-items-center small mb-5">
                <div class="rating mr-2">
                    @rateUid@
                </div>
                <a class="link-underline" href="#commentList">{Отзывы}: @avgRateNum@</a>
                @brandUidDescription@
            </div>
            <!-- End Rating -->

            <!-- Title -->
            <div class="mb-5 small mb-0">
                @productContent@
            </div>
            <!-- End Title -->
            <!-- Блок сравнить -->
            <div class="media align-items-center mb-4 text-primary ">
                <span class="w-100 max-w-4rem mr-0">
                    <i class="fas fa-heart nav-icon"></i>
                </span>
                <div class="text-body small mr-4">
                    <a class="text-primary addToWishList" data-uid="@productUid@" href="#">{В избранное}</a>
                </div>

                <span class="w-100 max-w-4rem mr-0">
                    <i class="fas fa-sliders-h nav-icon"></i>
                </span>
                <div class="media-body text-body small">
                    <a class="text-primary addToCompareList" data-uid="@productUid@" href="#">{В сравнение}</a>
                </div>

            </div>
            <!-- Конец блока Сравнить -->

            <!-- Цена -->
            <div class="align-items-center mb-2 @hideCatalog@" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <span class="text-dark font-size-2 font-weight-bold">
                    <span itemprop="price" class="priceService" content="@productSchemaPrice@">@productPrice@</span> 
                    <span itemprop="priceCurrency" class="rubznak" content="RUB">@productValutaName@</span>  
                </span>
                <span class="text-body ml-4 @php __hide('productPriceOld'); php@"><del class="price-old">@productPriceOld@</del> @specIcon@</span>
            </div>

            <div class="option-block">
                <!-- Опции -->
                @optionsDisp@

                <!-- Подтипы -->
                @productParentList@
            </div>

            <!-- Количество -->
            <div class="@hideCatalog@">    
                <div class="text-body space-bottom-1 small" id="items">@productSklad@</div>
                @ComStartNotice@
                <div class="text-danger space-bottom-1 small">@productOutStock@</div>
                @ComEndNotice@
            </div>


            <div class="border py-2 px-3 mb-4 @elementCartHide@ @hideCatalog@">
                <div class="js-quantity-counter row align-items-center">
                    <div class="col-7">
                        <small class="d-block text-body font-weight-bold">{Выберите количество}:</small>
                        <input class="js-result form-control h-auto border-0 rounded-lg p-0" type="text" value="1" name="quant[1]">
                    </div>
                    <div class="col-5 text-right">
                        <a class="js-minus btn btn-xs btn-icon btn-outline-secondary rounded-circle" href="javascript:;">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="js-plus btn btn-xs btn-icon btn-outline-secondary rounded-circle" href="javascript:;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border rounded-lg py-2 px-3 mb-3 @elementCartOptionHide@ @hideCatalog@">
                <div class="js-quantity-counter row align-items-center">
                    <div class="col-7">
                        <small class="d-block text-body font-weight-bold">{Выберите количество}:</small>
                        <input class="js-result form-control h-auto border-0 rounded-lg p-0" type="text" value="1" name="quant[2]">
                    </div>
                    <div class="col-5 text-right">
                        <a class="js-minus btn btn-xs btn-icon btn-outline-secondary rounded-circle" href="javascript:;">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="js-plus btn btn-xs btn-icon btn-outline-secondary rounded-circle" href="javascript:;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Модуль услуги -->
            @productservices_list@

            <!-- Вывод стикера Аккордеон для карточки товара -->
            @sticker_accordion@

            <div class="mb-2 @elementCartHide@ @hideCatalog@">
                <button type="button" class="btn btn-block btn-primary  transition-3d-hover addToCartFull" data-num="1" data-uid="@productUid@">@flowProductSale@</button>
            </div>
            <div class="mb-2 @elementCartOptionHide@ @hideCatalog@">
                <button type="button" class="btn btn-block btn-primary  transition-3d-hover addToCartFull" data-num="1" data-uid="@productUid@">@flowProductSale@</button>
            </div>

            <!-- Модуль Купить в 1 клик -->
            @oneclick@          

            <!-- Модуль Vkseller -->
            <div class="mb-4 @php __hide('vkseller_link'); php@">
                <a class="btn btn-block btn-soft-success  transition-3d-hover" href="@vkseller_link@" target="_blank">{Купить в ВКонтакте}</a>
            </div>

            <!-- Модуль Ozonseller -->
            <div class="mb-4 @php __hide('ozonseller_link'); php@">
                <a class="btn btn-block btn-soft-success transition-3d-hover" href="@ozonseller_link@" target="_blank">{Купить в} OZON</a>
            </div>

            <!-- Модуль Wbseller -->
            <div class="mb-4 @php __hide('wbseller_link'); php@">
                <a class="btn btn-block btn-soft-success  transition-3d-hover" href="@wbseller_link@" target="_blank">{Купить в} Wildberries</a>
            </div>
            
            @deliverywidget@

        </div>
        <!-- End Product Description -->
    </div>
</div>
<!-- End Hero Section -->

<!-- Product Description Section -->
<div class="container">

    <div class="row">
        <div class="col-md-7 mb-6 ">
            <div class="@php __hide('productDes'); php@">
                <h4>{Описание}</h4>
                <div class="pr-lg-4 small mb-3">
                    @productDes@
                </div>
            </div>


            <h4>{Отзывы}</h4>
            <div class="pr-lg-4 mb-5">
                <div id="commentList"></div>
                <!--<a class="comment-more hide-click link-underline" href="#">{Показать еще}</a><br>-->
                <a href="#" class="link-underline" data-toggle="modal" data-target="#reviewModal">{Оставить отзыв}</a>

                <script type="text/javascript">
                    $(document).ready(function () {
                        commentList('@productUid@', 'list');
                    });
                </script>

            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="@php __hide('vendorDisp'); php@">
                <h4>{Характеристики}</h4>

                <div class="pr-lg-4 small">
                    @vendorDisp@
                </div>
            </div>

            @productFilesStart@
            <div class="mt-3">
                <h4>{Файлы}</h4>

                <div class="pr-lg-4">
                    @productFiles@
                </div>
            </div>
            @productFilesEnd@

            <div class="mt-3 @php __hide('pagetemaDisp'); php@">
                <h4>{Статьи}</h4>

                <div class="pr-lg-4">
                    @pagetemaDisp@
                </div>
            </div>


        </div>
        <!-- End Product Description Section -->

    </div>
    
            <div class="w-lg-100 border-top space-2 mx-lg-auto @php __hide('productlastview'); php@">
        <div class="mb-3 mb-sm-5">
            <h4>@productlastview_title@</h4> 
        </div>
        <!-- Slick Carousel -->
        <div class="js-slick-carousel slick slick-gutters-3 slick-equal-height z-index-2 mx-md-auto mb-5 mb-md-9" data-hs-slick-carousel-options='{
             "slidesToShow": 5,
             "slidesToScroll": 3,
             "dots": true,
             "dotsClass": "slick-pagination",
             "responsive": [{
             "breakpoint": 1200,
             "settings": {
             "slidesToShow": 5
             }
             }, {
             "breakpoint": 992,
             "settings": {
             "slidesToShow": 4
             }
             }, {
             "breakpoint": 768,
             "settings": {
             "slidesToShow": 3
             }
             }, {
             "breakpoint": 554,
             "settings": {
             "slidesToShow": 2,
             "slidesToScroll": 2

             }
             }]
             }'>

            @productlastview@
        </div>
        <!-- End Slick Carousel -->

    </div>

    <div class="w-lg-100 border-top space-2 mx-lg-auto @php __hide('productlist_list'); php@">
        <div class="mb-3 mb-sm-5">
            <h4>@productlist_title@</h4> 
        </div>
        <!-- Slick Carousel -->
        <div class="js-slick-carousel slick slick-gutters-3 slick-equal-height z-index-2 mx-md-auto mb-5 mb-md-9" data-hs-slick-carousel-options='{
             "slidesToShow": 6,
             "slidesToScroll": 3,
             "dots": true,
             "dotsClass": "slick-pagination",
             "responsive": [{
             "breakpoint": 1200,
             "settings": {
             "slidesToShow": 5
             }
             }, {
             "breakpoint": 992,
             "settings": {
             "slidesToShow": 4
             }
             }, {
             "breakpoint": 768,
             "settings": {
             "slidesToShow": 3
             }
             }, {
             "breakpoint": 554,
             "settings": {
             "slidesToShow": 2,
             "slidesToScroll": 2

             }
             }]
             }'>

            @productlist_list@
        </div>
        <!-- End Slick Carousel -->

    </div>
      <div class="w-lg-100 border-top space-2 mx-lg-auto @php __hide('productsimilar_list'); php@">
        <div class="mb-3 mb-sm-5">
            <h4>@productsimilar_title@</h4> 
        </div>
        <!-- Slick Carousel -->
        <div class="js-slick-carousel slick slick-gutters-3 slick-equal-height z-index-2 mx-md-auto mb-5 mb-md-9" data-hs-slick-carousel-options='{
             "slidesToShow": 5,
             "slidesToScroll": 3,
             "dots": true,
             "dotsClass": "slick-pagination",
             "responsive": [{
             "breakpoint": 1200,
             "settings": {
             "slidesToShow": 5
             }
             }, {
             "breakpoint": 992,
             "settings": {
             "slidesToShow": 4
             }
             }, {
             "breakpoint": 768,
             "settings": {
             "slidesToShow": 3
             }
             }, {
             "breakpoint": 554,
             "settings": {
             "slidesToShow": 2,
             "slidesToScroll": 2

             }
             }]
             }'>

            @productsimilar_list@
        </div>
        <!-- End Slick Carousel -->

    </div>
    
</div>