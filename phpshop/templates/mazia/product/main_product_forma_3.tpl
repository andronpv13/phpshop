<div class="col-6 col-lg-4 col-md-4 px-2 px-sm-3 mb-3 mb-sm-5">
    <!-- Product -->
    <div class="product-box mb-40">
        <div class="product-box-wrapper">
            <div class="product-img">
                <img src="@productImg@" alt="@productName@" loading="lazy" >
                <a href="/shop/UID_@productUid@.html" title="@productName@" class="d-block">
                    <div class="second-img">
                        <img src="@productImg@" alt="@productName@" loading="lazy">
                    </div>
                </a>
<a href="#" data-role="/shop/UID_@productUid@.html" class="product-img-link quick-view-1 text-capitalize fastView" data-toggle="modal" data-target="#modalProductView"><i class="fal fa-search"></i> {Превью}
                </a>
                <span class="sale">
                @specIcon@
                @hitIcon@
                @newtipIcon@
                @promotionsIcon@
                </span>
            </div>

            <div class="product-desc pb-20">
                <div class="product-desc-top">
                    <a href="#" class="wishlist pr-10 addToWishList @elementCartHide@" data-uid="@productUid@" title="{Отложить}"><span><i class="fal fa-heart"></i></span></a>
                </div>
                <a href="/shop/UID_@productUid@.html" class="product-title">
                    @productName@
                </a>

                <div class="price-switcher">
                    <span class="price switcher-item">
                        <span class="text-dark font-weight-bold">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span></span>
                        <span class="text-body small ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span>
                    </span>

                    <div class="switcher-item @hideCatalog@">
                        <a href="/shop/UID_@productUid@.html" class="add-cart text-capitalize @elementCartOptionHide@">@productSale@</a>
                        <a href="#" class="add-cart text-capitalize addToCartList @elementCartHide@" data-uid="@productUid@">@productSale@</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Product -->
</div>