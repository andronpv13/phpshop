<!-- Products -->
<li class="card card-bordered shadow-none mb-3 mb-md-5 w-100">
    <div class="row no-gutters">
        <div class="col-md-4">
            <img class="card-img" src="@productImg@" alt="@productName@" loading="lazy">
        </div>

        <div class="col-md-8">
            <div class="card-body">

                <div class="m-0">
                    @hitIcon@
                    @specIcon@
                    @newtipIcon@
                    @promotionsIcon@
                </div>

                <div class="mb-2">

                    <a class="text-inherit" title="@productName@" href="/shop/UID_@productUid@.html">@productName@</a>

                    <div class="pt-2 @hideCatalog@">
                        <span class="h5">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span></span>
                        <span class="text-body ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span>

                    </div>
                </div>
                <div class="mb-2">

                    <div class="d-inline-flex align-items-center small" >
                        <div class="rating text-warning mr-2">
                            @rateCid@                  
                        </div>
                        <span class="@php __hide('avgRateNum'); php@ text-primary">@avgRateNum@</span>
                    </div>

                </div>
                <a class="btn btn-sm btn-outline-primary btn-pill transition-3d-hover @elementCartOptionHide@ @hideCatalog@" href="/shop/UID_@productUid@.html">@productSale@</a>
                <button type="button" class="btn btn-sm btn-outline-primary btn-pill transition-3d-hover addToCartList @elementCartHide@ @hideCatalog@" data-uid="@productUid@">@flowProductSale@</button>

                <button type="button" class="btn btn-sm btn-soft-primary btn-pill transition-3d-hover addToWishList @elementCartHide@ @hideCatalog@" data-uid="@productUid@">
                    <i class="far fa-heart mr-1"></i> {Отложить}
                </button>
            </div>
        </div>
    </div>
</li>
<!-- End Products -->