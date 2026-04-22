

<div class="product-block-wrapper-fix similar-fix">
    <div class="thumbnail">
       
        <div class="product-btn">
        <button class=" addToCompareList " data-uid="@productUid@"><span class="icons icons-green icons-small icons-compare"></span></button>
            <button class=" addToWishList @elementCartHide@" data-uid="@productsimilar_product_id@"><span class="icons icons-green icons-small icons-wishsimilar"></span></button>
        </div>

        <div class="caption ">
        <div class="d-flex  justify-content-between align-items-start last-block">
            
<div class="last-info">
            <div class="product-name"><a href="@shopDir@@productsimilar_product_url@.html" title="@productName@">@productsimilar_product_name@</a></div>
                        <div class="d-flex justify-content-between align-items-start">
                <div class="product-price">
                    <div class=" price-old  @php __hide('productsimilar_product_price_old'); php@">@productsimilar_product_price_old@</div>
                    <div class="price-new">@productsimilar_product_price@<span class="rubznak">@productlastview_product_currency@</span></div>

                </div>
              
            </div>
            </div>
            <div class="product-image position-relative">
                <a href="@shopDir@@productsimilar_product_url@.html" title="@productsimilar_product_name@"><img alt="@productsimilar_product_name@" class="swiper-lazy" src="@productsimilar_product_pic_small@"></a>
            </div>
            </div>

           
        </div>
       
    </div>
</div>