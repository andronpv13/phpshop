
<div class="col-md-3 col-sm-3 col-xs-6 product-block-wrapper-fix">
    <div class="product-block-top">
        <a class="product-img" href="/shop/UID_@productUid@.html"  title="@productName@"><img data-src="@productImg@" alt="@productName@" class="swiper-lazy"></a>
		  <span class="sale-icon-content ">
					@specIcon@
					@newtipIcon@
					<div class="label-block"> @hitIcon@
					 @promotionsIcon@</div>
				</span>
        <div class="product-block-button">
		<a class="wrap-link" href="/shop/UID_@productUid@.html"  title="@productName@"></a>
            <a href="#" data-role="/shop/UID_@productUid@.html" class="btn btn-cart fastView btn-circle" data-toggle="modal" data-target="#modalProductView"><span class="icons-search"></span></a>
            <div class="btn-block"> 
			<a class="btn addToCartList @elementCartOptionHide@" data-title="{Выбрать}" data-placement="top" data-toggle="tooltip" href="/shop/UID_@productUid@.html"><span class="icons-cart"></span></a>
                <button class="btn  addToCartList @elementCartHide@" data-uid="@productUid@" data-num="1" role="button" data-title="{ упить}" data-placement="top" data-toggle="tooltip"><span class="icons-cart"></span></button>

                <button class="btn btn-wishlist addToWishList" role="button" data-uid="@productUid@" data-title="{ќтложить}" data-placement="top" data-toggle="tooltip"><span class="icons-like"></span></button>
                <button class="btn btn-wishlist addToCompareList" role="button" data-uid="@productUid@" data-title="{—равнить}" data-placement="top" data-toggle="tooltip"><span class="icons-compare"></span></button>

                <a class="btn btn-cart @elementNoticeHide@" href="/users/notice.html?productId=@productUid@" title="@productNotice@"  data-title="@productNotice@" data-placement="top" data-toggle="tooltip">
                    <span class="icons-mail"></span>
                </a>
            </div>

        </div></div>

    <a href="/shop/UID_@productUid@.html" class="caption">
        <div class="price-block">

            <h4 class="new-price">@parentLangFrom@ @productPrice@ <span class="rubznak">@productValutaName@</span></h4>
            <h5 class="old-price">@productPriceOld@</h5>
        </div>
        <div class="rating">
            @rateCid@
        </div>
        <h5 class="product-name">@productName@</h5>


    </a>
</div>