
<div class="col-md-3 col-sm-3 col-xs-6">
    <div class="thumbnail">
        <span class="sale-icon-content">
            @specIcon@
            @newtipIcon@
            @hitIcon@
            @promotionsIcon@
        </span>      
        <div class="caption">
            <a class="product-image" href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <img data-src="@productImg@" alt="@productNameClean@">
            </a>

            <h5><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h5>
            <h4 class="product-price @hideCatalog@">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span><span class=" price-old">@productPriceOld@</span></h4>
            <div class="stock @hideCatalog@">
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
                @previewSorts@
                <span class="product-sklad-list-block">@productSklad@</span>
            </div>
        </div>
        <div class="caption @hideCatalog@">
            <a class="btn btn-primary addToCartList btn-sm btn-block @elementCartOptionHide@" href="/shop/UID_@productUid@.html">@productSale@</a>
            <button class="btn btn-primary btn-sm btn-block addToCartList @elementCartHide@" data-uid="@productUid@">@productSale@</button>
            <a class="btn btn-primary btn-sm btn-block  @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@" >
                @productNotice@                         
            </a>  
            <button class="btn btn-default addToWishList btn-sm btn-block" data-uid="@productUid@">{Отложить}</button>

        </div>
    </div>
</div>
