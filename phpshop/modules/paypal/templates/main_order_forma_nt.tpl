<SCRIPT language="JavaScript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript" src="java/jqfunc.js"></SCRIPT>
<link href="phpshop/lib/templates/order/style.css" type="text/css" rel="stylesheet">
@order_action_add@
<form method="post" name="forma_order" action="/done/">
    <div id="checkout">
        <div id="checkout">
            <b>Заказ №</b>
            <input type="text" name=ouid style="width:50px; height:18px; font-family:tahoma; font-size:11px ; color:#9e0b0e; background-color:#f2f2f2;" value="@orderNum@"  readonly="1">
            <b>/</b>
            <input type="text" style="width:50px; height:18px; font-family:tahoma; font-size:11px ; color:#9e0b0e; background-color:#f2f2f2;" value="@orderDate@"  readonly="1"><BR>

            <BR><BR>
            <h2>Личные данные</h2>

            <div class="checkout-heading">Шаг 1: Личные данные@authData@</div>
            @noAuth@
        </div>
        <div id="checkout">
            <div class="checkout-heading">Шаг 2: Доставка, адрес получателя</div>
            <div class="checkout-content" style="display: block;">
                <div class="left">
                    <h2>Способ доставки</h2>

                    @orderDelivery@ 
                    <BR><BR>
                    @UserAdresList@
                </div>
                <div id="login" class="right">
                    <h2>Адрес доставки</h2>
                    @noAuthAdr@
                    <div id="userAdresData1">

                        <div id="citylist">
                            <span class="required">*</span>
                            Страна
                            <br>
                            <select name="country_new" class="citylist req">
                                <option value="" for="0">-----------</option>
                                <option value="RU">Россия</option>
                                <option value="BY">Белоруссия</option>
                                <option value="UA">Украина</option>
                                <option value="KZ">Казахстан</option>
                            </select>
                        </div>

                        <span class="required">*</span>
                        Город
                        <br>
                        <input type="text" value="" name="city_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        Индекс
                        <br>
                        <input type="text" value="" name="index_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        Улица
                        <br>
                        <input type="text" value="" name="street_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        Дом
                        <br>
                        <input type="text" value="" name="house_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        Квартира
                        <br>
                        <input type="text" value="" name="flat_new" class="req">
                        <br><br>

                    </div>
                    <br>
                    Дополнительная информация к заказу: 
                    <textarea style="width:300px; height:100px; font-family:tahoma; font-size:11px ; color:#4F4F4F " name="dop_info" ></textarea>
                </div>
            </div>
            <BR>
        </div>
        <div id="checkout">
            <div class="checkout-heading">Шаг 3: Способ оплаты</div>
            <BR>
            <div class="checkout-content" style="display: block;">
                @orderOplata@
                <br>
                <br>
                <div id="showYurDataForPaymentLoad">
                </div>
            </div>
        </div>
        <div id="checkout">
            <div class="checkout-heading">Шаг 4: Оформить заказ</div>
            <BR>
            <div class="checkout-content" style="display: block;">
                <div class="left">
                    <img src="images/shop/brick_error.gif" border="0" align="absmiddle"> <a href="javascript:forma_order.reset();" class=link>Очистить форму</a>
                    <input type="hidden" name="send_to_order" value="ok" >
                    <input type="hidden" name="d" id="d" value="@deliveryId@">
                    <input type="hidden" name="nav" value="done">
                </div>
                <div id="login" class="right">
                    <img src="images/shop/brick_go.gif"  border="0" align="absmiddle"> <a href="javascript:OrderChekJq();" class=link>Оформить заказ</a>
                </div>
            </div>
        </div>
    </div>
</form>
@showYurDataForPayment@