<div class="col-md-4 col-sm-3 col-xs-6">
    <div class="thumbnail">
        <span class="sale-icon-content">
            @specIcon@
            @newtipIcon@
            @hitIcon@
            @promotionsIcon@
        </span>
        <a  class="product-image" href="/shop/UID_@productUid@.html" title="@productNameClean@">
            <img data-src="@productImg@" alt="@productNameClean@">
        </a>
        <div class="caption description">
            <h4><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productNameClean@</a></h4>
            @productDes@
        </div>
        @previewSorts@
        <div class="btn-sale @hideCatalog@">

            <h4 class="product-price">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span><span class=" price-old">@productPriceOld@</span></h4>
            <div class="stock">
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
                <span class="product-sklad-list-block">@productSklad@</span>
            </div>
            <a class="btn btn-primary addToCartList btn-sm @elementCartOptionHide@" href="/shop/UID_@productUid@.html">@productSale@</a>
            <button class="btn btn-primary btn-sm addToCartList @elementCartHide@" data-uid="@productUid@">@productSale@</button>
            <a class="btn btn-primary btn-sm   @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@" >
                @productNotice@                         
            </a>  
            <button class="btn btn-default addToWishList btn-sm" data-uid="@productUid@">{Отложить}</button></div>
    </div>
</div>