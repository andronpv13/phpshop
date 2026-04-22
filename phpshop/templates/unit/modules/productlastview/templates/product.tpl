



<div class="product-block-wrapper-fix list-fix">
    <div class="thumbnail">
       
        <div class="product-btn">
        
        <button class=" addToWishList @elementCartHide@" data-uid="@productlastview_product_id@"><span class="icons icons-green icons-small icons-wishlist"></span></button>
        <button class=" addToCompareList " data-uid="@productlastview_product_id@"><span class="icons icons-green icons-small icons-compare"></span></button>
        </div>
        <div class="caption ">
           <a href="@shopDir@@productlastview_product_url@.html" title="@productlastview_product_name@">
        <div class="d-flex  justify-content-between align-items-start last-block">
            
<div class="last-info">
            <div class="product-name">@productlastview_product_name@</div>
                        <div class="d-flex justify-content-between align-items-start">
                <div class="product-price">
               
                    <div class=" price-old @php __hide('productlastview_product_price_old'); php@">@productlastview_product_price_old@</div>
                    <div class="price-new">@productlastview_product_price@<span class="rubznak">@productlastview_product_currency@</span></div>

                </div>
              
            </div>
            </div>
            <div class="product-image position-relative">
                <img data-src="@productlastview_product_pic_small@" alt="@productlastview_product_name@" class="swiper-lazy" src="@productlastview_product_pic_small@">
            </div>
            </div>
</a>
           
        </div>

    </div>
</div>