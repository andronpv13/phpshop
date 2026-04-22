<div class="product-box mb-40 w-100">
    <div class="product-box-wrapper">
        <div class="list-product mb-50 ">
            <div class="list-product-wrapper">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-4">
                        <div class="list-product-img">
                            <div class="product-img">
                                <img src="@productImg@" alt="@productName@" loading="lazy" class="w-100">
                                <a href="/shop/UID_@productUid@.html" title="@productName@" class="d-block">
                                    <div class="second-img">
                                        <img src="@productImg@" alt="@productName@" loading="lazy" class="w-100">
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
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8 col-md-8">
                        <div class="list-product-desc">
                            <div class="categories">

                            </div>
                            <h3><a href="/shop/UID_@productUid@.html" class="title mb-15">@productName@</a></h3>

                            <div class="d-inline-flex align-items-center small" >
                                <div class="rating text-warning mr-2">
                                    @rateCid@                  
                                </div>
                                <span class="@php __hide('avgRateNum'); php@ text-primary">@avgRateNum@</span>
                            </div>

                            <div class="price">
                                <span class="price switcher-item">
                                    <span class="text-dark font-weight-bold">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span></span>
                                    <span class="text-body small ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span>
                                </span>
                            </div>
                            <div class="desc">
                                <p>@productDes@</p>

                                <a href="/shop/UID_@productUid@.html" class="list-add-cart-btn text-capitalize mt-40  @elementCartOptionHide@ @hideCatalog@">@productSale@</a>
                                <a href="/shop/UID_@productUid@.html" class="list-add-cart-btn text-capitalize mt-40 addToCartList @elementCartHide@ @hideCatalog@" data-uid="@productUid@">@productSale@</a>
                               <a href="#" class="wishlist pr-10 addToWishList @elementCartHide@" data-uid="@productUid@" title="{Отложить}"><span><i class="fal fa-heart"></i></span></a>
                                <a href="#" title="{Сравнить}" class="addToCompareList" data-uid="@productUid@"><span><i class="fal fa-abacus"></i></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /. list product -->
    </div>
</div>
