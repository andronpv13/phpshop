
<div class="col-md-3 col-sm-3 col-xs-12 product-block-wrapper-fix column-5">
   <div class="thumbnail">
        <!--<span class="sale-icon-content d-flex flex-column align-items-start">
            @promotionsIcon@
            @newtipIcon@
            @hitIcon@
            
        </span>-->
        <div class="product-btn">  <a href="?delete=@prodId@" title="{Удалить}" class="wish-delete" ><span class="icons icons-blue icons-small icons-close"></span></a></div>
        <div class="caption ">
        <a href="/shop/UID_@prodId@.html" title="@prodId@">
            <div class="product-image position-relative">
                <img class="template-wishlist-list" src="@prodPic@" alt="@prodName@"  title="@prodName@">
            </div>

            <div class="product-name">@prodName@</div>
           
        </a>
            <div class="d-flex justify-content-between align-items-end">
            
                <div class="product-price d-flex flex-column align-items-end justify-content-end">
                   
                    <div class="price-new">@prodPrice@<span class="rubznak">@productValutaName@</span></div>

                </div>
               <div class="d-flex flex-column align-items-end justify-content-end"> <span class="">
                        @specIcon@</span>
                    <!--<a class=" addToCartList @elementCartOptionHide@" href="/shop/UID_@productUid@.html">@productSale@ <span class="icons icons-cart"></span></a>-->
                    <button class=" addToCartList @wishlistCartHide@" title="{В корзину}" onclick="addToCartList('@prodId@');" @prodDisabled@>{В корзину} <span class="icons icons-cart"></span></button>
                         
                </div>
            </div>
           
        </div>
       
    </div>
</div>