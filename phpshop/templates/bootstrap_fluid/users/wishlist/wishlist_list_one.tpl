<tr>
    <td class="visible-lg">            
        <a href="/shop/UID_@prodId@.html"><img class="template-wishlist-list" src="@prodPic@" alt="@prodName@"  title="@prodName@"></a>
    </td>
    <td ><a href="/shop/UID_@prodId@.html">@prodName@</a></td>
    <td >            
        <div class="price">
            @prodPrice@ <span class="rubznak">@productValutaName@</span>
        </div>
    </td>
    <td >
        <a class="btn btn-success btn-sm @wishlistCartHide@" title="{В корзину}" onclick="addToCartList('@prodId@');" @prodDisabled@>+</a>
        <a href="?delete=@prodId@" title="{Удалить}" class="btn btn-info btn-sm hidden-xs">X</a>
    </td>
</tr>