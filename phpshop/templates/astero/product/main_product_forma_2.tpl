<div class="col-md-6 col-sm-6 product-block-wrapper">
    <span class="sale-icon-content">
        @specIcon@
        @newtipIcon@
        @hitIcon@
        @promotionsIcon@            
    </span>
    <div class="product-col">
        <div class="image product-img-centr">
            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <img data-src="@productImg@" alt="@productNameClean@">
            </a>
        </div>
        <div class="caption">
            <h4 class="product-name-fix">
                <a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a>
            </h4>
            <div class="description product-description">
                <div class="description-content">
                    @productDes@
                </div>
                <div class="description-product-height-fix"></div>
            </div>
            @previewSorts@
            <div class="price @hideCatalog@">
                <span class="price-new">@productPrice@<span class="rubznak">@productValutaName@</span></span> 
                <span class="price-old">@productPriceOld@</span>
            </div>
            <div class="stock @hideCatalog@">
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
                <span class="product-sklad-list-block">@productSklad@</span>
            </div>            
            <div class="cart-button button-group @hideCatalog@">
                <a class="btn btn-cart @elementCartOptionHide@" href="/shop/UID_@productUid@.html">
                    <i class="icon-basket"></i>
                    <span>@productSale@</span>
                </a>

                <button type="button" class="btn btn-cart addToCartList @elementCartHide@" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                    <i class="icon-basket"></i>                     
                    <span>@productSale@</span>
                </button>

                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><i class="icon-heart"></i></button>
                <button class="btn btn-wishlist addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><i class="icon-sliders"></i></button>

                <a class="btn btn-cart @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                    <i class="icon-mail" aria-hidden="true"></i>                            
                    {Уведомить}
                </a>                                   


            </div>
        </div>
    </div>
</div>