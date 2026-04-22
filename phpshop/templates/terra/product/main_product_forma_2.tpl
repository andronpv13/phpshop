<div class="col-md-6 col-sm-6">
    <div class="product-col">

        <div class="image product-img-centr">
            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <img data-src="@productImg@" alt="@productNameClean@" class="img-responsive img-center-sm" >
            </a>
        </div>

        <div class="rating">@rateCid@</div>
        <span class="previewsorts">@previewSorts@</span>
        <div class="caption">
            <h4><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h4>
            <div class="price @hideCatalog@">
                <span class="price-new">@parentLangFrom@ @productPrice@ <span class="rubznak">@productValutaName@</span></span> 
                <span class="price-old">@productPriceOld@</span>
            </div>
            <div class="stock @hideCatalog@">
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@
            </div>
            <div class="cart-button button-group @hideCatalog@">
                <a class="btn btn-cart addToCartList @elementCartOptionHide@" href="/shop/UID_@productUid@.html" data-title="{Выбрать}" data-placement="top" data-toggle="tooltip"><span class="icons-cart"></span>@productSale@</a>

                @ComStartCart@
                <button type="button" class="btn btn-cart addToCartList" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">{Купить}</button>
                @ComEndCart@

                @ComStartNotice@
                <a class="btn btn-cart" href="/users/notice.html?productId=@productUid@" title="@productNotice@">{Уведомить}</a>                                   
                @ComEndNotice@ 

                <button class="btn btn-compare addToCompareList" data-uid="@productUid@"><i class="fa fa-sliders" aria-hidden="true"></i>{Сравнить}</button>
                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@"><i class="fa fa-heart-o" aria-hidden="true"></i>{Отложить}</button>

            </div>
        </div>
        <div class="sale-icon-content">
            @specIcon@
            @newtipIcon@
            @hitIcon@
            @promotionsIcon@
        </div>
    </div>
</div>