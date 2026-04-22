<div class="col-md-4 col-sm-4 product-block-wrapper">
    <div class="product-block">
        <div class="product-block-top">
            <div class="product-block-image">
                <div class="sale-icon-content">
                    @specIcon@
                    @newtipIcon@
                    @hitIcon@
                    @promotionsIcon@
                </div>
                <a class="product-block-img-link" href="/shop/UID_@productUid@.html" title="@productNameClean@">
                    <img data-src="@productImg@" alt="@productNameClean@" class="owl-lazy">
                </a>
            </div>
        </div>
        <div class="product-block-bottom">
            <h3 class="product-block-name product-name-fix">
                <a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a>
            </h3>
            @previewSorts@
            <h4 class="product-block-price @hideCatalog@">
                <span class="price-old">@productPriceOld@</span>
                <span class="price-new">@parentLangFrom@ @productPrice@ <span class="rubznak">@productValutaName@</span></span>
            </h4>

            <span class="product-sklad-list-block @hideCatalog@">@productSklad@
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
            </span>
            <div class="product-block-button @hideCatalog@">
                <button class="btn btn-wishlist addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><i class="fa fa-bar-chart-o"></i></button>
                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><i class="feather iconz-heart"></i></button>
                <a class="btn btn-cart @elementCartOptionHide@" href="/shop/UID_@productUid@.html">
                    <span>@productSale@</span>
                </a>
                <button type="button" class="btn btn-cart addToCartList @elementCartHide@" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                    <span>@productSale@</span>
                </button>
                <a class="btn btn-cart @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                    {Уведомить}
                </a>
            </div>
        </div>
    </div>
</div>