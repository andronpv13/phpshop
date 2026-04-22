<div class="col-6 col-lg-3 px-2 px-sm-3 mb-3 mb-sm-5">
    <!-- Product -->
    <div class="card shadow-soft transition-3d-hover text-center" style="min-height:440px;">
        <a class="text-inherit" title="@productName@" href="/shop/UID_@productUid@.html">
            <div class="position-absolute top-0 left-0 pt-2 pl-2">
                @hitIcon@
                @specIcon@
                @newtipIcon@
            </div>
            <div class="position-absolute top-0 right-0 pt-2 pr-2">
                <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-circle addToWishList @elementCartHide@" data-uid="@productUid@" title="{Отложить}">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
            <div class="card-flex pt-2">
                <img class="card-img-top" src="@productImg@" alt="@productName@" loading="lazy">
            </div>
            <div class="pt-2 @hideCatalog@">
                <span class="text-dark font-weight-bold">@parentLangFrom@ @productPrice@<span class="rubznak">@productValutaName@</span></span>
                <span class="text-body small ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span>
            </div>

            <div class="card-body pt-2 px-2 pb-0" >
                <div>@promotionsIcon@</div>
                <div class="m-2 goods-name">
                    <span class="d-block small">
                        @productName@
                    </span>
                </div>
            </div>

            <div class="card-footer border-0 pt-0 pb-2 px-1">
                <div class="mb-1">
                    <div class="d-inline-flex align-items-center small" >
                        <div class="rating text-warning mr-2">
                            @rateCid@                  
                        </div>
                        <span class="@php __hide('avgRateNum'); php@ text-primary">@avgRateNum@</span>
                    </div>
                </div>
            </div></a>
        <a class="btn btn-sm btn btn-soft-primary @elementCartOptionHide@ @hideCatalog@" href="/shop/UID_@productUid@.html @hideCatalog@">@productSale@</a>
        <button type="button" class="btn btn-sm btn btn-soft-primary addToCartList @elementCartHide@ @hideCatalog@" data-uid="@productUid@ @hideCatalog@">@productSale@</button>
    </div> 
    <!-- End Product -->
</div>