@pochta_order_info@
<div class="row" style="padding-bottom: 20px;">
    <div class="col-sm-12 pochta_actions" style="@pochta_hide_actions@">
        <button type="button" class="btn btn-sm btn-primary pochta-change-address">{Изменить}</button>
        <button type="button" class="btn btn-sm btn-success pochta-send">{Отправить заказ}</button>
    </div>
</div>
<input type="hidden" name="pochta_order_id" value="@pochta_order_id@">
<style>
    #pochta-table .checkbox-inline {
        padding-top: 0!important;
    }
</style>
<script src="https://widget.pochta.ru/courier/widget/widget.js"></script>
<script src="https://widget.pochta.ru/map/widget/widget.js"></script>
<div class="modal fade bs-example-modal" id="pochtaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{Доставка}</h4>
            </div>
            <div class="modal-body" style="width:100%;">
                <div id="pochta-frame" style="height: 600px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="pochta-close">{Закрыть}</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="pochta_widget_id" value="@pochta_widget_id@">
<input type="hidden" name="pochta_courier_widget_id" value="@pochta_courier_widget_id@">
<input type="hidden" name="pochta_weight" value="@pochta_weight@">
<input type="hidden" name="pochta_ins_value" value="@pochta_ins_value@">
<script src="../modules/pochta/templates/pochta.js"></script>