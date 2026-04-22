<div class="col-xs-12 product-block-wrapper-fix">
    <span class="sale-icon-content">
        @specIcon@
        @newtipIcon@
        @hitIcon@
        @promotionsIcon@
    </span>
    <div class="product-col list clearfix">
        <div class="image">
            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <img data-src="@productImg@" alt="@productNameClean@" class="swiper-lazy">
            </a>
        </div>
        <div class="caption">
            <h4><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h4>
            <div class="description">
                @productDes@
            </div>
            @previewSorts@
            <div class="price @hideCatalog@">
                <span class="price-new">@productPrice@ <span class="rubznak">@productValutaName@</span></span> 
                <span class="price-old">@productPriceOld@</span>
            </div>
            <div class="flex-block"> @ComStartNotice@
                <div class="outStock @hideCatalog@">@productOutStock@</div>
                @ComEndNotice@

                <span class="product-sklad-list-block @hideCatalog@">@productSklad@</span>
                <div class="cart-button button-group @hideCatalog@">
                    <a class="btn btn-cart @elementCartOptionHide@" href="/shop/UID_@productUid@.html">
                        <i class="fa fa-shopping-cart"></i>
                        <span>@productSale@</span>
                    </a>

                    <button type="button" class="btn btn-cart addToCartList @elementCartHide@" data-uid="@productUid@" data-cart="@productSaleReady@">
                        <i class="fa fa-shopping-cart"></i>                             
                        <span>@productSale@</span>
                    </button>
                    <a class="btn btn-cart @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                        <i class="fa fa-envelope-o" aria-hidden="true"></i>                            
                        {Уведомить}
                    </a>                                   

                    <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><i class="fa fa-heart"></i></button>
                    <button class="btn btn-wishlist addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><i class="fa fa-bar-chart-o"></i></button>


                </div>
            </div>
        </div>
    </div>
</div>