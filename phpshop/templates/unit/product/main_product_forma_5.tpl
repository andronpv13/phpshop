<div class="col-md-3 col-sm-3 col-xs-12 product-block-wrapper-fix column-5" >
    <div class="thumbnail">
        <span class="sale-icon-content d-flex flex-column align-items-start">
            @promotionsIcon@
            @newtipIcon@
            @hitIcon@

        </span>
        <div class="product-btn @hideCatalog@"> 
            <button class=" addToCompareList " data-uid="@productUid@"><span class="icons icons-green icons-small icons-compare"></span></button>
            <a class=" addToWishList @elementCartOptionHide@" href="/shop/UID_@productUid@.html"><span class="icons icons-green icons-small icons-wishlist"></span></a>
            <button class=" addToWishList @elementCartHide@" data-uid="@productUid@"><span class="icons icons-green icons-small icons-wishlist"></span></button>
        </div>
        <div class="caption ">
            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <div class="product-image position-relative">
                    <img data-src="@productImg@" alt="@productNameClean@" class="swiper-lazy" src="@productImg@">
                </div>

                <div class="product-name">@productName@</div>
                <div class="stock">
                    @previewSorts@

                </div>
                <div class="rating">
                    @rateCid@
                </div>

            </a>
            <div class="d-flex justify-content-between align-items-end @hideCatalog@">

                <div class="product-price d-flex flex-column align-items-start justify-content-end">
                    <div class=" price-old @php __hide('productPriceOld'); php@"  >@productPriceOld@</div>
                    <div class="price-new">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span></div>

                </div>
                <div class="d-flex flex-column align-items-end justify-content-end"> <span class="">
                        @specIcon@</span>
                    <a class=" addToCartList @elementCartOptionHide@" href="/shop/UID_@productUid@.html">@productSale@ <span class="icons icons-cart"></span></a>
                    <button class=" addToCartList @elementCartHide@" data-uid="@productUid@">@productSale@ <span class="icons icons-cart"></span></button>
                    <button class="notice-btn  @elementNoticeHide@" title="@productNotice@" data-product-id="@productUid@">
                        @productNotice@
                    </button>
                </div>
            </div>

        </div>
        <div class="caption">
        </div>
    </div>
</div>