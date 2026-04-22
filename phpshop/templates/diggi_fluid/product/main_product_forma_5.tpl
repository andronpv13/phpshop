<div class="col-auto col-sm-3 col-xs-6 product-block-wrapper-fix">
    <span class="sale-icon-content">
        @specIcon@
        @newtipIcon@
        @hitIcon@
        @promotionsIcon@
    </span>
    <div class="product-col">
        <div class="image product-img-centr">
            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <img data-src="@productImg@" alt="@productNameClean@" class="img-responsive img-center-sm swiper-lazy" >
            </a>
        </div>
        <div class="caption">
            <h4><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h4>
            <div class="description product-description @php __hide('productDes'); php@">
                <div class="description-content">
                    @productDes@
                </div>
                <div class="description-product-height-fix"></div>
            </div>
            @previewSorts@
            <div class="description-link-wrapper">
                <a class="description-link" href="/shop/UID_@productUid@.html" title="@productNameClean@"></a>
            </div>
            <div class="price @hideCatalog@">
                <span class="price-new">@productPrice@ <span class="rubznak">@productValutaName@</span></span>
                <span class="price-old">@productPriceOld@</span>
            </div>
            <div class="stock @hideCatalog@">  @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
                <span class="product-sklad-list-block">@productSklad@</span>
            </div>
            <div class="cart-button button-group @hideCatalog@">
                <a class="btn btn-cart @elementCartOptionHide@" href="/shop/UID_@productUid@.html"  data-title="@productSale@" data-toggle="tooltip">
                    <i class="fa fa-shopping-cart"></i>                     
                </a>
                <button type="button" class="btn btn-cart addToCartList @elementCartHide@" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@" data-title="@productSale@" data-toggle="tooltip">
                    <i class="fa fa-shopping-cart"></i>                     
                </button>
                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><i class="fa fa-heart"></i></button>

                <a class="btn btn-cart @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@"  data-title="@productNotice@" data-placement="top" data-toggle="tooltip">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>                            
                </a>                                   

            </div>
        </div>
    </div>
</div>