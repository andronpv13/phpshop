<div class="panel panel-default">
    <div class="panel-body position-relative">
        <div class="media ">
            <span class="sale-icon-content">
                @specIcon@
                @newtipIcon@
                @hitIcon@
                @promotionsIcon@
            </span>
            <div class="product-btn @hideCatalog@"> 
                <button class=" addToCompareList " data-uid="@productUid@"><span class="icons icons-green icons-small icons-compare"></span></button>
                <button class=" addToWishList " data-uid="@productUid@"><span class="icons icons-green icons-small icons-wishlist"></span></button>
            </div>
            <a class="media-left text-center" href="/shop/UID_@productUid@.html" style="min-width:200px" title="@productNameClean@">
                <img data-src="@productImg@" alt="@productNameClean@">
            </a>
            <div class="media-body">
                <div class="product-name"><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></div>
                <p>@productDes@</p>
                @previewSorts@
                <div class="h4 product-price @hideCatalog@">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span><sup class="text-muted">@productPriceOld@</sup></div>
                <div class="d-flex justify-content-between @hideCatalog@">
                    <div class="stock">
                        @ComStartNotice@
                        <div class="outStock">@productOutStock@</div>
                        @ComEndNotice@
                        <span class="product-sklad-list-block">@productSklad@</span>
                    </div>
                    <div class="pull-right">
                        <a class=" addToCartList @elementCartOptionHide@" href="/shop/UID_@productUid@.html">@productSale@ <span class="icons icons-cart"></span></a>
                        <button class=" addToCartList @elementCartHide@" data-uid="@productUid@">@productSale@ <span class="icons icons-cart"></span></button>
                        <button class="notice-btn  @elementNoticeHide@" title="@productNotice@" data-product-id="@productUid@">
                            @productNotice@
                        </button>

                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>