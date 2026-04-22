<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title"><a href="/shop/UID_@productUid@.html">@productName@</a></h1></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
    </div>

    <div class="modal-body">

        <div class="row">
            <div class="col-md-6 product-img">  

                <span class="sale" style="left: 15px">
                    @specIcon@
                    @hitIcon@
                    @newtipIcon@
                    @promotionsIcon@
                </span>
                @productSliderOneImage@


            </div>
            <div class="col-md-6">

                <div class="col-xs-12">
                    <div class="row">
                        <div class="flex-block">


                            <!-- Цена -->
                            <div class="single-product-price d-flex align-items-center mb-2" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                <span>
                                    <span itemprop="price" class="priceService" content="@productSchemaPrice@">@productPrice@</span> 
                                    <span itemprop="priceCurrency" class="rubznak" content="RUB">@productValutaName@</span>  
                                </span>
                                <span class="text-body light ml-4 @php __hide('productPriceOld'); php@"><del>@productPriceOld@</del> @specIcon@</span>
                            </div>



                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="prodict-page-description">
                            @productContent@
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <span class="product-art">@productArt@</span>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="product-promotions">
                            @promotionInfo@
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row modal-input-fix">
                        @optionsDisp@
                        <div class="odnotip-@productUid@ parrent-wrapper">
                            @productParentList@
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="product-content">

                            <!-- В корзину -->

                            <div class=" py-2 px-3 mb-4 @elementCartHide@ @hideCatalog@">
                                <div class="js-quantity-counter row align-items-center">
                                    <form action="#" method="POST">
                                        <input type="number" class="mb-20" value="1" name="quant[1]"  min="1" style="margin-right: 20px; width: 119px;">
                                        <button type="button" data-num="1" data-uid="@productUid@" class="list-add-cart-btn red-hover-btn border-0 addToCartFull" style="padding-left: 80px;padding-right: 80px;transition: all .5s;">@flowProductSale@</button>		
                                    </form>
                                </div>
                            </div> 

                            <div class=" py-2 px-3 mb-4 @elementCartOptionHide@ @hideCatalog@">
                                <div class="js-quantity-counter row align-items-center">
                                    <form action="#" method="POST">
                                        <input type="number" class="mb-20" value="1" name="quant[1]"  min="1" style="margin-right: 20px; width: 119px;">
                                        <button type="button" data-num="1" data-uid="@productUid@" class="list-add-cart-btn red-hover-btn border-0 addToCartFull" style="padding-left: 80px;padding-right: 80px;transition: all .5s;">@flowProductSale@</button>		
                                    </form>
                                </div>
                            </div> 
                            <!-- Кнопка Под заказ -->
                            @ComStartNotice@
                            <div class="mb-4 mt-4 @hideCatalog@">
                                <a class="list-add-cart-btn red-hover-btn border-0" href="/users/notice.html?productId=@productUid@" title="@productNotice@">{Уведомить}</a>
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


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>