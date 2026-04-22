<tr class="order-page-product-block">
    <td class="order-product-img">
        <a href="/shop/UID_@cart_id@.html" title="@cart_name@">
            <img src="@cart_pic_small@" alt="@cart_name@" title="@cart_name@" class="img-thumbnail">
        </a>
    </td>
    <td class="order-product-name">
        <a href="/shop/UID_@cart_id@.html" title="@cart_name@">@cart_name@</a>
    </td>                           
    <td class="order-product-quantity">
        <div class="order-product-quantity-wrapper order-page-num-input-fix  hide">
            <form name="forma_cart_plus" method="post" id="forma_cart_plus">
                <button type="submit" class="btn tool-tip" data-toggle="tooltip" data-placement="top" title="+1">
                    <i class="icon-right-dir" aria-hidden="true"></i>
                </button>
                <input type=hidden name="id_edit" value="@cart_xid@">
                <input type=hidden name="edit_num" value="edit">
                <input type=hidden name="num_new" value="@cart_num@">
            </form>
            <form name="forma_cart" method="post" id="forma_cart">
                <input class="" type="text" value="@cart_num@" size="3" maxlength="5" name="num_new" onchange="this.form.submit()">
                <input type=hidden name="id_edit" value="@cart_xid@">
            </form>
            <form name="forma_cart_minus" method="post" id="forma_cart_minus">
                <button type="submit" class="btn tool-tip" data-toggle="tooltip" data-placement="top" title="-1">
                    <i class="icon-left-dir" aria-hidden="true"></i>
                </button>
                <input type=hidden name="id_edit" value="@cart_xid@">
                <input type=hidden name="edit_num" value="minus">
                <input type=hidden name="num_new" value="@cart_num@">
            </form>
        </div>
    </td>
        <td class="order-product-all-price">
        @cart_price_all@ @currency@ <br><s class="text-muted">@cart_price_all_old@</s>
    </td>
    <td class="order-product-remove-product">
        <form name="forma_cart_del" method="post" id="forma_cart_del">
            <button type="submit" class="btn btn-default tool-tip" data-toggle="tooltip" data-placement="top" title="{Удалить}">
                <i class="icon-cancel" aria-hidden="true"></i>
            </button>
            <input type=hidden name="id_delete" value="@cart_xid@">
        </form>
    </td>
</tr>