
<SCRIPT language="JavaScript" type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript" src="java/jqfunc.js"></SCRIPT>
@order_action_add@
<link href="phpshop/lib/templates/orde&#114/style.css" type="text/css" rel="stylesheet">
<form method="post" name="forma_order" action="/done/">
    <div id="checkout">
        <div id="checkout">
            <h2>Личные данные</h2>
            <div class="checkout-heading">№ Заказа</div>
            <p>
            <input type="text" name="ouid" style="width:100px;" value="@orderNum@" readonly="1"> <b>/</b> <input type="text" style="width:100px;" value="@orderDate@"  readonly="1"><br></p>
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
                    <div id="userAdresData">
                    </div>
                    <br>
                    Дополнительная информация<br>
                    <textarea style="width:200px; height:100px; font-family:tahoma; font-size:11px ; color:#4F4F4F" name="dop_info" id="dop_info"></textarea>
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