
@checkLabelForOldTemplatesNoDelete@
@order_action_add@

<!-- Form Starts -->
<form class="form-horizontal" method="post" name="forma_order" id="forma_order" action="/done/">
    <div class="row">
    
        <!-- Shipping & Shipment Block Starts -->
            <div class="col-sm-12">
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {Личные данные}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <input type="hidden" name="ouid" value="@orderNum@" readonly="1">
                        <input type="hidden" value="@orderDate@"  readonly="1">
                        @authData@ 
                        @noAuth@
                    </div>
                </div>
                <!-- Taxes Block Starts -->
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {Доставка, адрес получателя}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <div class="col-md-12 radio">
                                    @orderDelivery@      
                                </div>    
                            </div>
                            @UserAdresList@                            
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            @noAuthAdr@
                            <div id="userAdresData">
                            </div>
                            <div class="form-group">
                                <label for="dop_info" class="col-sm-12 control-label fix">{Дополнительная информация к заказу}</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="dop_info" id="dop_info"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Shipping & Shipment Block Ends -->
        <!-- Discount & Conditions Blocks Starts -->
            <div class="col-sm-12">
                <div class="panel panel-smart">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {Способ оплаты}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-12">
                                @orderOplata@
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-8">
                                <div id="showYurDataForPaymentLoad"></div>
                            </div>
                            <div class="col-sm-12">
                                        
                                        <div class="text-uppercase text-center clearfix" style="margin-top: 10px;">
                                            <input type="hidden" name="send_to_order" value="ok" >
                                            <input type="hidden" name="d" id="d" value="@deliveryId@">
                                            <input type="hidden" name="nav" value="done">
                                            <button type="submit" class="btn btn-main orderCheckButton">{Оформить заказ}</button>
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Discount & Conditions Blocks Ends -->
        <!-- Total Panel Starts -->
        
        <!-- Total Panel Ends -->
    </div>
</form>
<!-- Form Ends -->
@showYurDataForPayment@