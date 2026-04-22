<div class="col-auto col-sm-4 col-lg-3 px-2 px-sm-2 mb-2 mb-sm-2">
    <!-- Product -->
    <div class="product-wrapper mb-45 text-center h-100">
        <div class="product-img">
            <a class="text-inherit" title="@productName@" href="/shop/UID_@productUid@.html"><img class="card-img-top" src="@productImg@" alt="@productName@" loading="lazy"> </a>
            <span class="text-center @hideCatalog@">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span>
                <span class="text-body small ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span></span>

            <div class="product-action @elementCartHide@ @hideCatalog@">
                <div class="product-action-style"> 
                    <a href="#" class="addToWishList " title="{Отложить}" data-uid="@productUid@"> <i class="fa fa-heart"></i> </a> 
                    <a href="#" class="addToCartList " title="{В корзину}" data-uid="@productUid@"> <i class="fa fa-shopping-cart"></i> </a> 
                </div>
            </div>
        </div>

        <div class="card-body pt-2 px-2 pb-0" >
            <span class="pt-2 pl-2">
                @hitIcon@
                @specIcon@
                @newtipIcon@
            </span>

            <div>@promotionsIcon@</div>
            <div class="m-2 goods-name">
                <span class="d-block small">
                    <a class="text-inherit" title="@productName@" href="/shop/UID_@productUid@.html">@productName@</a>
                </span>
            </div>
        </div>

    </div>
    <!-- End Product -->
</div>