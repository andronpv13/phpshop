<tr class="product-tr">
    <td  colspan="2">
        <div class="flex align-center prod-img-name flex-column"> <span class="product-cart-img"><a href="/shop/UID_@cart_id@.html" title="@cart_name@"><img src="@cart_pic_small@" border="0" hspace="5" vspace="5" align="absmiddle" style="max-width: 40px; max-height: 40px;"></a></span>

            <a href="/shop/UID_@cart_id@.html" title="@cart_name@">@cart_name@</a></div>
    </td>
       <td>
        <div class="cart-btn-wrap"><table cellpadding="0" cellspacing="0" class="cart-btn-block">
                <tr>
                    <td  style="width:23px!important" class="minus">
                        <form name="forma_cart_minus" method="post" id="forma_cart_minus">
                            <button type="submit" class="cart-minus" >-</button>
                            <input type=hidden name="id_edit" value="@cart_xid@">
                            <input type=hidden name="edit_num" value="minus">
                            <input type=hidden name="num_new" value="@cart_num@">
                        </form>
                    </td>
                    <td align="center">
                        <form name="forma_cart" method="post" id="forma_cart">
                            <input type="text" value="@cart_num@" size="3" maxlength="5" name="num_new" class="cart-input" onchange="this.form.submit()">
                            <input type=hidden name="id_edit" value="@cart_xid@">
                        </form>
                    </td>


                    <td style="width:23px!important" class="plus">
                        <form name="forma_cart_plus" method="post" id="forma_cart_plus">
                            <button type="submit" class="cart-plus " >+</button>
                            <input type=hidden name="id_edit" value="@cart_xid@">
                            <input type=hidden name="edit_num" value="edit">
                            <input type=hidden name="num_new" value="@cart_num@">
                        </form>
                    </td>


                </tr>
            </table>
    </td>


    <td align="right"><span class="nowrap">@cart_price_all@ <span class="rubznak">@currency@</span>
            <br><s class="text-muted">@cart_price_all_old@</s>
        </span></td>
    <td >
        <table  align="center">
            <tr>

                <td style="padding-left:5px">
                    <form name="forma_cart_del" method="post" id="forma_cart_del">
                        <button type="submit" class="cart-delete-wrap label"  ><span class="cart-delete">+</span> <span class="close-text"></span></button>
                        <input type=hidden name="id_delete" value="@cart_xid@">
                    </form>
                </td>
            </tr>
        </table>
    </td>
</tr>