<!-- single product start -->
<section class="single-product mb-90" itemscope itemtype="http://schema.org/Product">
    <div class="">
        <meta itemprop="image" content="@productImg@">
        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
            <meta itemprop="ratingValue" content="@productRatingValue@">
            <meta itemprop="ratingCount" content="@productRatingCount@">
        </div>

        <div class="shop-wrapper">
            <div class="single-product-top">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-4 col-12">
                        <div class="shop-img">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content product-img" id="v-pills-tabContent">
                                        <span class="sale" style="left: 15px">
                                            @specIcon@
                                            @hitIcon@
                                            @newtipIcon@
                                            @promotionsIcon@
                                        </span>
                                        @productFotoList@

                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="nav nav-pills has-border-img mt-25" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        @productHeroSliderNav@
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-12">
                        <div class="single-product-sidebar">
                            <div class="product-content">
                                <div class="single-product-title">
                                    <h1 itemprop="name" class="h2 page-title">
                                        @productName@
                                    </h1>                     
                                </div>
                                <!-- Цена -->
                                <div class="single-product-price d-flex align-items-center mb-2" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                    <span>
                                        <span itemprop="price" class="priceService" content="@productSchemaPrice@">@productPrice@</span> 
                                        <span itemprop="priceCurrency" class="rubznak" content="RUB">@productValutaName@</span>  
                                    </span>
                                    <span class="text-body light ml-4 @php __hide('productPriceOld'); php@"><del>@productPriceOld@</del> @specIcon@</span>
                                </div>

                                <div class="single-product-desc mb-25">
                                    @productContent@                                    
                                </div>

                                <div class="option-block">
                                    <!-- Опции -->
                                    @optionsDisp@

                                    <!-- Подтипы -->
                                    @productParentList@
                                </div>

                                <!-- Количество -->
                                <div class="total-cart">
                                    <span class="cart-count">
                                        <div class="text-body space-bottom-1 " id="items">@productSklad@</div>
                                        <div class="">@productArt@</strong</div>
                                        @ComStartNotice@
                                        <div class="text-danger space-bottom-1 ">@productOutStock@</div>
                                        @ComEndNotice@
                                    </span>
                                </div>

                                <!-- В корзину -->

                                <div class=" py-2 px-3 mb-4 @elementCartHide@ @hideCatalog@">
                                    <div class="js-quantity-counter row align-items-center">
                                        <form action="#" method="POST">
                                            <input type="number" class="mb-20" value="1" name="quant[1]" min="1" style="margin-right: 20px; width: 119px;">
                                            <button type="button" data-num="1" data-uid="@productUid@" class="list-add-cart-btn red-hover-btn border-0 addToCartFull" style="padding-left: 80px;padding-right: 80px;transition: all .5s;">@flowProductSale@</button>		
                                        </form>
                                    </div>
                                </div> 

                                <div class=" py-2 px-3 mb-4 @elementCartOptionHide@ @hideCatalog@">
                                    <div class="js-quantity-counter row align-items-center">
                                        <form action="#" method="POST">
                                            <input type="number" class="mb-20" value="1" name="quant[1]" min="1" style="margin-right: 20px; width: 119px;">
                                            <button type="button" data-num="1" data-uid="@productUid@" class="list-add-cart-btn red-hover-btn border-0 addToCartFull" style="padding-left: 80px;padding-right: 80px;transition: all .5s;">@flowProductSale@</button>		
                                        </form>
                                    </div>
                                </div> 
                                <!-- Кнопка Под заказ -->
                                @ComStartNotice@
                                <div class="col-xs-5 @hideCatalog@">
                                    <a class="btn btn-primary" href="/users/notice.html?productId=@productUid@" title="@productNotice@">{Уведомить}</a>
                                </div>
                                @ComEndNotice@ 

                                <!-- Rating -->
                                <div class="d-flex align-items-center small mb-5">
                                    <div class="rating mr-2">
                                        @rateUid@
                                    </div>
                                    {Отзывы}: @avgRateNum@
                                    @brandUidDescription@
                                </div>
                                <!-- End Rating -->


                                <!-- Блок сравнить -->
                                <div class="single-product-action mt-35">
                                    @wholesaleInfo@
                                    <ul>
                                        <li><a class="addToWishList" data-uid="@productUid@" href="#" ><i class="fal fa-heart"></i>{Отложить}</a>
                                        </li>
                                        <li><a class="addToCompareList" data-uid="@productUid@" href="#"><i class="fal fa-abacus"></i> {Сравнить}</a></li>
                                    </ul>
                                </div>
                                <!-- Конец блока Сравнить -->

                                <div class="single-product-category">

                                    <!-- Модуль услуги -->
                                    @productservices_list@

                                    <!-- Вывод стикера Аккордеон для карточки товара -->
                                    @sticker_accordion@

                                    <!-- Модуль Купить в 1 клик --> 
                                    @oneclick@ 

                                    <!-- Модуль Vkseller -->
                                    <div class="mb-4 @php __hide('vkseller_link'); php@">
                                        <a class="list-add-cart-btn text-capitalize" href="@vkseller_link@" target="_blank">{Купить в ВКонтакте}</a>
                                    </div>

                                    <!-- Модуль Ozonseller -->
                                    <div class="mb-4 @php __hide('ozonseller_link'); php@">
                                        <a class="list-add-cart-btn text-capitalize" href="@ozonseller_link@" target="_blank">{Купить в} OZON</a>
                                    </div>

                                    <!-- Модуль Wbseller -->
                                    <div class="mb-4 @php __hide('wbseller_link'); php@">
                                        <a class="list-add-cart-btn text-capitalize" href="@wbseller_link@" target="_blank">{Купить в} Wildberries</a>
                                    </div>

                                    <!-- Модуль Yandexcart -->
                                    <div class="mb-4 @php __hide('yandexmarket_link'); php@">
                                        <a class="list-add-cart-btn text-capitalize" href="@yandexmarket_link@" target="_blank">{Купить в Яндекс.Маркете}</a>
                                    </div>

                                    @deliverywidget@

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="single-product-bottom mt-80 gray-border-top">
                    <ul class="nav nav-pills justify-content-center mt-100" id="pills-tab" role="tablist">
                        <li class="nav-item @php __hide('productDes'); php@">
                            <a class="active" data-toggle="pill" href="#desc-tab-1">{Описание}</a>
                        </li>
                        <li class="nav-item @php __hide('vendorDisp'); php@">
                            <a data-toggle="pill" href="#desc-tab-3">{Характеристики}</a>
                        </li>
                        <li class="nav-item">
                            <a class="" data-toggle="pill" href="#desc-tab-2">{Отзывы}</a>
                        </li>
                        <li class="nav-item">
                            @productFilesStart@
                            <a data-toggle="pill" href="#desc-tab-4">{Файлы}</a>
                            @productFilesEnd@
                        </li>
                        <li class="nav-item @php __hide('pagetemaDisp'); php@">
                            <a data-toggle="pill" href="#desc-tab-5">{Статьи}</a>
                        </li>
                    </ul>
                    <div class="container container-1200">
                        <div class="tab-content mt-60" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="desc-tab-1">
                                <div class="single-product-tab-content">
                                    @productDes@
                                </div>
                            </div>
                            <div class="tab-pane fade" id="desc-tab-2">
                                <div class="single-product-tab-content">

                                    <div id="commentList"></div>
                                    <!--<a class="comment-more hide-click link-underline" href="#">{Показать еще}</a><br>-->
                                    <a href="#" class="generic-btn black-hover-btn" data-toggle="modal" data-target="#reviewModal">{Оставить отзыв}</a>

                                    <script type="text/javascript">
                                        $(document).ready(function () {
                                            commentList('@productUid@', 'list');
                                        });
                                    </script>

                                </div>
                            </div>

                            <!-- Характеристики -->
                            <div class="tab-pane fade" id="desc-tab-3">
                                <div class="single-product-tab-content ">
                                    @vendorDisp@

                                </div>
                            </div>

                            <!-- Файлы -->
                            <div class="tab-pane fade" id="desc-tab-4">
                                <div class="single-product-tab-content ">
                                    @productFiles@
                                </div>
                            </div>

                            <!-- Статьи -->
                            <div class="tab-pane fade" id="desc-tab-5">
                                <div class="single-product-tab-content ">
                                    @pagetemaDisp@

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<!-- single product end -->


