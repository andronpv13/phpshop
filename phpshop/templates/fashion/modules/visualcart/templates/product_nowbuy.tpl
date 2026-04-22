
<div class="modal-nowBuy @php __hide('nowbuy_close','cookie'); php@">

    <!-- Title -->
    <div class="border-bottom pb-2 mb-2">
        
        <button type="button" class="btn btn-xs btn-icon btn-soft-secondary float-right nowbuy-close">
          <svg aria-hidden="true" width="10" height="10" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
            <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
          </svg>
        </button>
        <h2 class="h4 mb-0">{Кто-то купил}</h2>
        

    </div>
    <!-- End Title -->

    <!-- Product Content -->
    <div class="pb-2 mb-2">
        <div class="media">
            <div class="avatar avatar-lg mr-3">
                <img class="avatar-img" src="@product_nowBuy_img@" alt="@product_nowBuy_name@">
                <sup class="avatar-status avatar-primary">1</sup>
            </div>
            <div class="media-body">
                <a href="/shop/UID_@product_nowBuy_id@.html"  class="h6" title="@product_nowBuy_name@">@product_nowBuy_name@</a>
                <div class="text-body font-size-1">
                    <span>{Цена}:</span>
                    <span>@product_nowBuy_price@</span> <span class="rubznak">@productValutaName@</span>
                </div>
                <div class="text-body font-size-1">
                    <span>{Осталось в наличии}:</span>
                    <span>@product_nowBuy_items@ {шт}.</span>
                </div>
            </div>
        </div>
    </div>
    <!-- End Product Content -->

</div>