<div class="col-6 col-lg-3 px-2 px-sm-3 mb-3 mb-sm-5">
    <!-- Product -->
    <div class="card card-bordered shadow-none text-center h-100">
        <div class="position-relative">
            <img class="card-img-top" src="@productImg@" alt="@productName@" loading="lazy">

            <div class="position-absolute top-0 left-0 pt-1 pl-1">
	            @wholesaleIcon@
                @hitIcon@
                @specIcon@
                @newtipIcon@
            </div>
            <div class="position-absolute bottom-0 left-0 pl-1 pb-1">
                @promotionsIcon@
            </div>
           
        </div>

        <div class="card-body pt-4 px-4 pb-0">
            <div class="mb-2">
                <span class="d-block font-size-1">
                    <a class="text-inherit" title="@productName@" href="/shop/UID_@productUid@.html">@productName@ </a>
                </span>
                <div class="@hideCatalog@">
                    <span class="text-dark font-weight-bold">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span></span>
                    <span class="text-body small ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span>
                </div>
            </div>
        </div>

        <div class="card-footer border-0 pt-0 pb-4 px-1">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center small" >
                    <div class="rating text-warning mr-2">
                        @rateCid@                  
                    </div>
                    <span class="@php __hide('avgRateNum'); php@ text-primary">@avgRateNum@</span>
                </div>
            </div>
            <a class="btn btn-sm btn-outline-primary btn-pill transition-3d-hover @elementCartOptionHide@@hideCatalog@" href="/shop/UID_@productUid@.html">@productSale@</a>
            <button type="button" class="btn btn-sm btn-outline-primary btn-pill transition-3d-hover addToCartList @elementCartHide@ @hideCatalog@" data-uid="@productUid@">@flowProductSale@</button>
        </div>
    </div>
    <!-- End Product -->
</div>