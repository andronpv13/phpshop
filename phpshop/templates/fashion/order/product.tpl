<!-- Product Content -->
<div class="border-bottom pb-5 mb-5">
    <div class="media">
        <div class="max-w-15rem w-100 mr-3 text-center">
            <img class="img-fluid-order" src="@cart_pic_small@" title="@cart_name@">
        </div>
        <div class="media-body">
            <div class="row">
                <div class="col-md-7 mb-3 mb-md-0">
                    <a class="h5 d-block" href="/shop/UID_@cart_id@.html" title="@cart_name@">@cart_name@</a>

                    <div class="d-block d-md-none">
                        <span class="h5 d-block mb-1">@cart_price_all@<span class="rubznak">@currency@</span></span>
                    </div>

                    <div class="text-body font-size-1 mb-1 d-none d-sm-block">
                        <span>{Артикул}:</span>
                        <span>@cart_art@</span>
                    </div>
                    
                    <div class="text-body font-size-1 mb-1 d-none d-sm-block @php __hide('cart_weight'); php@">
                        <span>{Вес}:</span>
                        <span>@cart_weight@ {г}</span>
                    </div>
                    
                </div>

                <div class="col-md-3">
                    <div class="row">
                        <div class="cart-btn-wrap mb-auto">
                            <table cellpadding="0" cellspacing="0" class="">
                                <tr>
                                    <td class="minus">
                                        <form name="forma_cart_minus" method="post" >
                                            <button type="submit" class="cart-minus" >-</button>
                                            <input type=hidden name="id_edit" value="@cart_xid@">
                                            <input type=hidden name="edit_num" value="minus">
                                            <input type=hidden name="num_new" value="@cart_num@">
                                        </form>
                                    </td>
                                    <td align="center">
                                        <form name="forma_cart" method="post" id="forma_cart">
                                            <input type="text" value="@cart_num@" size="3" maxlength="5" name="num_new" class="cart-input custom-select-sm" onchange="this.form.submit()">
                                            <input type=hidden name="id_edit" value="@cart_xid@">
                                        </form>
                                    </td>
                                    <td class="plus">
                                        <form name="forma_cart_plus" method="post" >
                                            <button type="submit" class="cart-plus " >+</button>
                                            <input type=hidden name="id_edit" value="@cart_xid@">
                                            <input type=hidden name="edit_num" value="edit">
                                            <input type=hidden name="num_new" value="@cart_num@">
                                        </form>
                                    </td>

                                </tr>
                            </table>
                        </div>

                        <div class="col-12">
                            <form name="forma_cart_del@cart_id@" method="post" id="forma_cart_del">

                                <input type=hidden name="id_delete" value="@cart_xid@">

                                <a class="d-block text-body font-size-1 mb-1" href="javascript:forma_cart_del@cart_id@.submit();">
                                    <i class="far fa-trash-alt text-hover-primary mr-1"></i>
                                    <span class="font-size-1 text-hover-primary">{Удалить}</span>
                                </a>
                            </form>

                            <a class="d-block text-body font-size-1 addToWishList" href="#" data-uid="@cart_xid@">
                                <i class="far fa-heart text-hover-primary mr-1"></i>
                                <span class="font-size-1 text-hover-primary">{Отложить}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-4 col-md-2 d-none d-md-inline-block text-right">
                    <span class="h5 d-block mb-1">@cart_price_all@ <span class="rubznak">@currency@</span>
                        <br><s class="text-muted">@cart_price_all_old@</s></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Product Content -->
