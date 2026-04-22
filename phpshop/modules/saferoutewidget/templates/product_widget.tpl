<script src="https://widgets.saferoute.ru/card/api.js?new"></script>
<script src="phpshop/modules/saferoutewidget/js/saferouteprodwidget.js"></script>
<div class="pull-right hidden-xs zn-delivery">
    <div>
        <button class="btn btn-cart" id="cartDelivery" role="button" >Стоимость доставки</button>
    </div>
</div>
<div class="modal fade bs-example-modal" id="saferoutewidgetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Доставка</h4>
            </div>
            <div class="modal-body" style="width:100%">
                 <div id="saferoute-card-widget"></div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="ddelivery-close">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#cartDelivery").on("click", function () {
        saferouteprodwidgetStart();
    })
</script>