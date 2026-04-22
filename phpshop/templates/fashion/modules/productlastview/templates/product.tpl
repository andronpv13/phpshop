
<div class="js-slide mt-1 mb-3">
    <!-- Product -->
    <div class="card shadow-soft transition-3d-hover text-center" style="min-height:400px;">
        <div class="position-relative">
            <img class="card-img-top" src="@productlastview_product_pic_small@" alt="@productlastview_product_name@" loading="lazy">
        </div>

        <div class="card-body pt-4 px-3 pb-0">
            <div class="mb-2">
                <span class="d-block font-size-1">
                    <a class="text-inherit" title="@productlastview_product_name@" href="@shopDir@@productlastview_product_url@.html">@productlastview_product_name@ </a>
                </span>
            </div>
        </div>


        <div class="card-footer border-0 pt-0 pb-1 px-1">
            <div class="d-block">
                <span class="text-dark font-weight-bold">@productlastview_product_price@<span class="rubznak">@productValutaName@</span></span>
                <span class="text-body ml-1 @php __hide('productlastview_product_price_old'); php@" ><del>@productlastview_product_price_old@</del></span>
            </div>
            <div class="mb-3">
                <div class="d-inline-flex align-items-center small">
                    <div class="rating text-warning mr-0">
                        <div class="rating_blank"></div>
                        <div class="rating_votes" style="width:@productlastview_product_rating@%"></div>
                    </div>
                </div>
            </div>
        </div>  

    </div>
    <!-- End Product -->
</div>
