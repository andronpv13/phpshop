<tr>
    <td>            
        <a class="d-none d-lg-block" href="/shop/UID_@prodId@.html" title="@prodName@"><img class="template-wishlist-list" src="@prodPic@" alt=""></a>
    </td>
    <td ><a href="/shop/UID_@prodId@.html">@prodName@</a></td>
    <td >            
        <div class="price">
            @prodPrice@ <span class="rubznak">@productValutaName@</span>
        </div>
    </td>
    <td class="text-right">
        <a class="btn btn-soft-success btn-xs @wishlistCartHide@" title="{В корзину}" onclick="addToCartList('@prodId@');" @prodDisabled@>+</a>
        <a href="?delete=@prodId@" title="{Удалить}" class="btn btn-soft-danger btn-xs">x</a>
    </td>
</tr>