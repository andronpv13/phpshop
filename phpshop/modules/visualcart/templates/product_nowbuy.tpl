
<div class="modal-nowBuy media @php __hide('nowbuy_close','cookie'); php@">
    <a href="#" class="nowbuy-close pull-right" title="{Закрыть}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
    <div class="media-left">
        <a href="/shop/UID_@product_nowBuy_id@.html" title="@product_nowBuy_name@">
            <img class="media-object" src="@product_nowBuy_img@" alt="@product_nowBuy_name@" title="@product_nowBuy_name@" style="max-width:100px; max-height:100px">
        </a>
    </div>
    <div class="media-body">
        {Кто-то купил}:
        <a href="/shop/UID_@product_nowBuy_id@.html"  class="media-heading" title="@product_nowBuy_name@"><h4>@product_nowBuy_name@</h4></a>
        @product_nowBuy_price@ <span class="rubznak">@productValutaName@</span>
        <p class="nowBuy-sklad">{В наличии}: @product_nowBuy_items@ {шт}.</p>
    </div>

</div>
<script>
    
    // Показ блока сейчас кто-то купил
    $(".modal-nowBuy").fadeIn(2000).delay(7000).fadeOut(1000);

    // Закрыть блок сейчас кто-то купил
    $('.nowbuy-close').on('click', function(e) {
        e.preventDefault();
        $('.modal-nowBuy').addClass('hide');
        $.cookie('nowbuy_close', 1, {
            path: '/',
            expires: 24
        });
    });
</script>