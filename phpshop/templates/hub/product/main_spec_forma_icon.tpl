<div class="col-xs-12 col-sm-6 col-md-3 product-block-wrapper">
    <div class="product-block">
        <div class="product-block-top">
            <div class="product-block-img">
                <a class="product-block-img-link" href="/shop/UID_@productUid@.html" title="@productNameClean@">
                    <img data-src="@productImg@" class="image-fix owl-lazy" alt="@productNameClean@">
                </a>
            </div>
            <div class="product-block-button @hideCatalog@">
                <a class="btn btn-cart @elementCartOptionHide@" href="/shop/UID_@productUid@.html" data-placement="top" data-toggle="tooltip" data-title="@productSale@" data-toggle="tooltip">
                    <i class="icons-cart"></i>
                </a>
                <button type="button" class="btn btn-cart addToCartList @elementCartHide@" data-placement="top" data-toggle="tooltip" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@" data-title="@productSale@" >
                    <i class="icons-cart"></i>
                </button>
                <a href="#" data-role="/shop/UID_@productUid@.html" class="btn btn-cart fastView" data-toggle="modal" data-target="#modalProductView" data-title="{Подробнее}" data-placement="top" data-toggle="tooltip"><i class="icons-view"></i></a>
                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><i class="icons-wishlist"></i></button>
                <button class="btn btn-wishlist addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><i class="icons-compare"></i></button>

                <a class="btn btn-cart @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@"  data-title="@productNotice@" data-placement="top" data-toggle="tooltip">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>                            
                </a>                             
            </div>
        </div>
        <div class="product-block-bottom">
            <h3 class="product-block-name product-name-fix">
                <a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a>
            </h3>
            <h4 class="product-block-price @hideCatalog@">
                <span class="price-new">@productPrice@<span class="rubznak">@productValutaName@</span></span><span class="price-old">@productPriceOld@</span>
            </h4>

            <div class="stock @hideCatalog@">
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
                <span class="product-sklad-list-block">@productSklad@</span>
            </div>            
            <span class="sale-icon-content">
                @specIcon@
                @newtipIcon@
                @hitIcon@
                @promotionsIcon@
            </span>

        </div>
    </div>
</div>