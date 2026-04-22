<!-- Products -->
<li class="card shadow-soft shadow-none mb-3 w-90">
    <div class="row no-gutters align-items-center">
        <div class="col-6 col-md-2 col-sm-3 text-center">
            <img class="card-img" style="border-radius: .25rem;" src="@productImg@" alt="@productName@" loading="lazy">
        </div>

        <div class="col-md-7 col-sm-7">
            <div class="card-body">
                <div class="m-0">
                    @hitIcon@
                    @specIcon@
                    @newtipIcon@
                    @promotionsIcon@
                </div>
                <div class="mb-2">

                    <a class="text-inherit small" title="@productName@" href="/shop/UID_@productUid@.html">@productName@</a>

                    <div class=" pt-2 @hideCatalog@">
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
            </div>
        </div>
        <div class="position-absolute top-0 right-0 pt-3 pr-3 @hideCatalog@">
            <a class="btn btn-xs btn-outline-secondary @elementCartOptionHide@" href="/shop/UID_@productUid@.html" title="@productSale@">@productSale@</a>
            <button type="button" title="{В корзину}" class="btn btn-xs btn-outline-secondary addToCartList @elementCartHide@" data-uid="@productUid@">
                @productSale@
            </button>
            <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-circle addToWishList @elementCartHide@" data-uid="@productUid@" title="{Отложить}">
                <i class="fas fa-heart"></i>
            </button>
        </div>

    </div>
</li>
<!-- End Products -->