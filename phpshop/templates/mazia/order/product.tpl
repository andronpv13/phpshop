<tr>
    <td>
        <div class="table-data">
            <form name="forma_cart_del@cart_id@" method="post" id="forma_cart_del">
                <input type=hidden name="id_delete" value="@cart_xid@">
                <a class="d-block text-body font-size-1 mb-1" href="javascript:forma_cart_del@cart_id@.submit();" title="{Удалить}">
                    <button class="close-btn"><i class="fal fa-times"></i></button>
                </a>
            </form>
        </div>
    </td>
    <td>
        <div class="table-data">
            <img src="@cart_pic_small@"  alt="" title="@cart_name@" class="" style="max-width:150px; max-height:150px">
        </div>
    </td>
    <td>
        <div class="table-data">
            <h6><a href="/shop/UID_@cart_id@.html" class="title" title="@cart_name@">@cart_name@</a></h6>
            <div class="@php __hide('cart_art'); php@">
                <span>{Артикул}:</span>
                <span>@cart_art@</span>
            </div>
            <div class="@php __hide('cart_weight'); php@">

                <span>{Вес}:</span>
                <span>@cart_weight@ {г}</span>
            </div>

        </div>
    </td>
    <td>
        <div class="table-data">
            <span class="price">@cart_price@<span class="rubznak">@currency@</span></span>
        </div>
    </td>
    <td>
        <div class="table-data">

            <form name="forma_cart" method="post" id="forma_cart">
                <input type="number" style="margin-right: 20px; width: 119px;" value="@cart_num@" size="3" maxlength="5" name="num_new" class="cart-input custom-select-sm" onchange="this.form.submit()">
                <input type=hidden name="id_edit" value="@cart_xid@">
            </form>
        </div>
    </td>
    <td>
        <div class="table-data">
            <span class="price">@cart_price_all@<span class="rubznak">@currency@</span></span>
        </div>
    </td>
</tr>