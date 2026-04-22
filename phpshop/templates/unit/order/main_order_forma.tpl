@checkLabelForOldTemplatesNoDelete@
@order_action_add@

<div class="page-header cart-header">
    <h2>{Заказ} &#8470;@orderNum@</h2>
</div>

<form method="post" name="forma_order" id="forma_order" action="/done/">

    <div class="panel panel-default no-margin">
        <input type="hidden" name="ouid" value="@orderNum@" readonly="1">
        <input type="hidden" value="@orderDate@"  readonly="1">
        <div class="panel-heading">
            <h3 class="panel-title">{Личные данные}</h3>
        </div>
        <div class="panel-body">
            @authData@ @noAuth@                  
            <label><input type="checkbox" value="on" name="rule" class="req" required="">  {Я согласен} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
            </label>
        </div>
    </div>


    <div class="panel panel-default no-margin">

        <div class="panel-heading">
            <h3 class="panel-title">{Доставка, адрес получателя}</h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-md-6">
                    <div class="radio">
                        @orderDelivery@
                    </div>  

                    @UserAdresList@

                </div>
                <div class="col-md-6">


                    @noAuthAdr@
                    <div id="userAdresData">
                    </div>

                    <textarea class="form-control" placeholder="{Дополнительная информация к заказу}" name="dop_info" id="dop_info"></textarea>

                </div>
            </div>

        </div>
    </div>


    <div class="panel panel-default no-margin border">
        <div class="panel-heading">
            <h3 class="panel-title">{Способ оплаты}</h3>
        </div>
        <div class="panel-body">

            <div class="radio">
                @orderOplata@
            </div>
            <br>
            <div id="showYurDataForPaymentLoad">
            </div>

        </div>
    </div>
    <div class="btn-border">

        <p class="text-left">
            <input type="hidden" name="send_to_order" value="ok" >
            <input type="hidden" name="d" id="d" value="@deliveryId@">
            <input type="hidden" name="nav" value="done">
            <button type="reset" class="btn btn-default btn-lg "> {Очистить}</button> 
            <button type="submit" class="btn btn-success btn-lg orderCheckButton"> {Оформить заказ}</button>
        </p>
    </div>

</form>
@showYurDataForPayment@