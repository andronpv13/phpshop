<tr>
    <td class="visible-lg wishlist-img-block">            
        <a href="/shop/UID_@prodId@.html"><img class="template-wishlist-list" src="@prodPic@" alt="@prodName@"  title="@prodName@"></a>
    </td>
    <td class="wishlst-fix-block"><a href="/shop/UID_@prodId@.html">@prodName@</a></td>
    <td class="wishlst-fix-block">            
        <div class="price">
            @prodPrice@ <span class="rubznak">@productValutaName@</span>
        </div>
    </td>
    <td class="wishlst-fix-block">
        <a class="btn btn-success btn-sm @wishlistCartHide@" title="{В корзину}" onclick="addToCartList('@prodId@');"><i class="fa fa-shopping-cart"></i></a>
        <a href="?delete=@prodId@" title="{Удалить из списка}" class="btn btn-info btn-sm hidden-xs">X</a>
    </td>
</tr>