<tr>
    <td  class="mobHideCol">
        <a href="/shop/UID_@cart_id@.html" title="@cart_name@"><img src="@cart_pic_small@" border="0" hspace="5" vspace="5" align="absmiddle" style="max-width: 40px; max-height: 40px;"></a>
    </td>
    <td>
        <a href="/shop/UID_@cart_id@.html" title="@cart_name@">@cart_name@</a>
    </td>
    <td>
        <table cellpadding="0" cellspacing="0" width="55">
            <tr>

                <td align="center">
                    <form name="forma_cart" method="post" id="forma_cart">
                        <input type="text" value="@cart_num@" size="3" maxlength="5" name="num_new" onchange="this.form.submit()">
                        <input type=hidden name="id_edit" value="@cart_xid@">
                    </form>
                </td>


            </tr>
        </table>
    </td>

    <td  class="mobHideCol">
        <table cellpadding="0" cellspacing="0" align="center"  width="30">
            <tr>
                <td width="50%">
                    <form name="forma_cart" method="post" id="forma_cart">
                        <input type="image" name="" src="phpshop/lib/templates/icon/cart_add.gif" value="" alt="{Пересчитать}" hspace="5" >
                        <input type=hidden name="id_edit" value="@cart_xid@">
                        <input type=hidden name="edit_num" value="edit">
                        <input type=hidden name="num_new" value="@cart_num@">
                    </form>
                </td>
                <td>
                    <form name="forma_cart" method="post" id="forma_cart">
                        <input type="image" name="" src="phpshop/lib/templates/icon/cart_minus.gif" value="" alt="{Пересчитать}" hspace="5" >
                        <input type=hidden name="id_edit" value="@cart_xid@">
                        <input type=hidden name="edit_num" value="minus">
                        <input type=hidden name="num_new" value="@cart_num@">
                    </form>
                </td>
            </tr>
        </table>
    </td>
    <td align="right" class="red">@cart_price@ @currency@</td>
    <td align="right" class="red">@cart_price_all@ @currency@  </td>
    <td align="right" class="red">
        <form name="forma_cart_del" method="post" id="forma_cart_del">
            <input type="image" name="edit_del" src="phpshop/lib/templates/icon/cart_delete.gif" value="delet" alt="{Удалить}">
            <input type=hidden name="id_delete" value="@cart_xid@">
        </form>
    </td>

</tr>