<div class="modal-nowBuy media @php __hide('nowbuy_close','cookie'); php@">
    <a href="#" class="nowbuy-close pull-right" title="{Закрыть}"><i class="fal fa-times" aria-hidden="true"></i></a>
    <div class="media-left">
        <a href="/shop/UID_@product_nowBuy_id@.html" title="@product_nowBuy_name@">
            <img class="media-object" src="@product_nowBuy_img@" alt="@product_nowBuy_name@" title="@product_nowBuy_name@" style="max-width:100px; max-height:100px">
        </a>
    </div>
    <div class="media-body">
        {Кто-то купил}:<br>
        <a href="/shop/UID_@product_nowBuy_id@.html"  class="media-heading" title="@product_nowBuy_name@">@product_nowBuy_name@</a><br>
        @product_nowBuy_price@ <span class="rubznak">@productValutaName@</span>
        <p class="nowBuy-sklad">{Осталось в наличии}: @product_nowBuy_items@ {шт}.</p>
    </div>

</div>