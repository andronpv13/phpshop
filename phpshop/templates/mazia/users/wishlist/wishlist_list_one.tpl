<tr>
    <td class="d-none d-lg-block">            
        <a href="/shop/UID_@prodId@.html" title="@prodName@"><img class="template-wishlist-list" src="@prodPic@" alt=""></a>
    </td>
    <td ><a href="/shop/UID_@prodId@.html">@prodName@</a></td>
    <td >            
        <div class="price">
            @prodPrice@
        </div>
    </td>
    <td class="text-right">
        <a class="@wishlistCartHide@" title="{В корзину}" onclick="addToCartList('@prodId@');" @prodDisabled@ href="#">+</a>
        &nbsp;&nbsp;
        <a href="?delete=@prodId@" title="{Удалить}" class="">x</a>
    </td>
</tr>